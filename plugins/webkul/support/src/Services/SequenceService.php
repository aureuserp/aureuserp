<?php

namespace Webkul\Support\Services;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\Sequence;

class SequenceService
{
    public static function next(string $code, ?int $companyId = null, array $defaults = [], ?CarbonInterface $date = null): string
    {
        $lookups = [['code' => $code, 'company_id' => $companyId]];

        if ($companyId !== null) {
            $lookups[] = ['code' => $code, 'company_id' => null];
        }

        return static::consume($lookups, ['code' => $code, 'company_id' => null], $defaults, $date);
    }

    public static function nextFor(Model $scope, string $variant = '', ?int $companyId = null, array $defaults = [], ?CarbonInterface $date = null): string
    {
        $keys = static::scopeKeys($scope, $variant, $companyId);

        return static::consume([$keys], $keys, $defaults, $date);
    }

    public static function ensure(string $code, ?int $companyId = null, array $defaults = []): Sequence
    {
        return static::resolve(['code' => $code, 'company_id' => $companyId], $defaults);
    }

    public static function ensureFor(Model $scope, string $variant = '', ?int $companyId = null, array $defaults = []): Sequence
    {
        return static::resolve(static::scopeKeys($scope, $variant, $companyId), $defaults);
    }

    public static function initialFromNames(Builder $query, string $column = 'name'): int
    {
        $highest = $query->pluck($column)
            ->map(fn ($name): int => preg_match('/(\d+)\D*$/', (string) $name, $matches) ? (int) $matches[1] : 0)
            ->max();

        return (int) $highest + 1;
    }

    protected static function scopeKeys(Model $scope, string $variant, ?int $companyId): array
    {
        return [
            'scope_type' => $scope->getMorphClass(),
            'scope_id'   => $scope->getKey(),
            'variant'    => $variant,
            'company_id' => $companyId,
        ];
    }

    protected static function consume(array $lookups, array $createKeys, array $defaults, ?CarbonInterface $date): string
    {
        return DB::transaction(function () use ($lookups, $createKeys, $defaults, $date): string {
            $sequence = null;

            foreach ($lookups as $keys) {
                if ($sequence = static::lockedQuery($keys)->first()) {
                    break;
                }
            }

            $sequence ??= static::createSequence($createKeys, $defaults);

            return $sequence->consumeNumber($date);
        });
    }

    protected static function resolve(array $keys, array $defaults): Sequence
    {
        return DB::transaction(function () use ($keys, $defaults): Sequence {
            return static::lockedQuery($keys)->first() ?? static::createSequence($keys, $defaults);
        });
    }

    public static function purge(array $codes = [], array $scopeModels = []): void
    {
        if (! Schema::hasTable('sequences')) {
            return;
        }

        if ($codes) {
            Sequence::withoutGlobalScope(CompanyScope::class)->whereIn('code', $codes)->delete();
        }

        foreach ($scopeModels as $scopeModel) {
            Sequence::withoutGlobalScope(CompanyScope::class)
                ->where('scope_type', (new $scopeModel)->getMorphClass())
                ->delete();
        }
    }

    public static function purgeScoped(string $scopeModel, array $scopeIds): void
    {
        if (empty($scopeIds) || ! Schema::hasTable('sequences')) {
            return;
        }

        Sequence::withoutGlobalScope(CompanyScope::class)
            ->where('scope_type', (new $scopeModel)->getMorphClass())
            ->whereIn('scope_id', $scopeIds)
            ->delete();
    }

    protected static function lockedQuery(array $keys): Builder
    {
        return Sequence::withoutGlobalScope(CompanyScope::class)
            ->where($keys)
            ->lockForUpdate();
    }

    protected static function createSequence(array $keys, array $defaults): Sequence
    {
        try {
            $sequence = DB::transaction(fn (): Sequence => Sequence::create([
                ...$keys,
                ...static::attributes($defaults),
            ]));
        } catch (UniqueConstraintViolationException) {
            return static::lockedQuery($keys)->firstOrFail();
        }

        return static::lockedQuery($keys)->whereKey($sequence->getKey())->firstOrFail();
    }

    protected static function attributes(array $defaults): array
    {
        $initial = $defaults['initial'] ?? null;

        if (is_callable($initial)) {
            $initial = (int) $initial();
        }

        if ($initial === null && isset($defaults['initial_from'])) {
            $initial = static::initialFromNames($defaults['initial_from']);
        }

        $initial ??= 1;

        $resetFrequency = $defaults['reset_frequency'] ?? SequenceResetFrequency::NEVER;

        return [
            'name'            => $defaults['name'] ?? null,
            'prefix'          => $defaults['prefix'] ?? null,
            'suffix'          => $defaults['suffix'] ?? null,
            'padding'         => $defaults['padding'] ?? 5,
            'step'            => $defaults['step'] ?? 1,
            'reset_frequency' => $resetFrequency instanceof SequenceResetFrequency ? $resetFrequency : SequenceResetFrequency::from($resetFrequency),
            'next_number'     => max(1, $initial),
        ];
    }
}
