<?php

namespace Webkul\Support\Services;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Models\Sequence;

class SequenceService
{
    public static function next(string $code, ?int $companyId = null, array $defaults = [], ?CarbonInterface $date = null): string
    {
        return static::consume([
            'code'       => $code,
            'company_id' => $companyId,
        ], $defaults, $date);
    }

    public static function nextFor(Model $scope, string $variant = '', ?int $companyId = null, array $defaults = [], ?CarbonInterface $date = null): string
    {
        return static::consume([
            'scope_type' => $scope->getMorphClass(),
            'scope_id'   => $scope->getKey(),
            'variant'    => $variant,
            'company_id' => $companyId,
        ], $defaults, $date);
    }

    protected static function consume(array $keys, array $defaults, ?CarbonInterface $date): string
    {
        return DB::transaction(function () use ($keys, $defaults, $date): string {
            $sequence = static::lockedQuery($keys)->first();

            $sequence ??= static::createSequence($keys, $defaults);

            return $sequence->consumeNumber($date);
        });
    }

    protected static function lockedQuery(array $keys): Builder
    {
        return Sequence::query()
            ->where($keys)
            ->lockForUpdate();
    }

    protected static function createSequence(array $keys, array $defaults): Sequence
    {
        try {
            $sequence = Sequence::create([
                ...$keys,
                ...static::attributes($defaults),
            ]);
        } catch (QueryException) {
            return static::lockedQuery($keys)->firstOrFail();
        }

        return static::lockedQuery($keys)->whereKey($sequence->getKey())->firstOrFail();
    }

    public static function initialFromNames(Builder $query, string $column = 'name'): int
    {
        $highest = $query->pluck($column)
            ->map(fn ($name): int => preg_match('/(\d+)\D*$/', (string) $name, $matches) ? (int) $matches[1] : 0)
            ->max();

        return (int) $highest + 1;
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
