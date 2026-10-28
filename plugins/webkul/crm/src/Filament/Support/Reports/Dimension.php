<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Closure;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Webkul\Crm\Enums\DimensionKind;

use function Illuminate\Support\enum_value;

class Dimension
{
    protected function __construct(
        public readonly string $label,
        public readonly string $expression,
        protected readonly DimensionKind $kind,
        protected readonly ?string $groupColumn = null,
        protected readonly ?Closure $resolveLabels = null,
        protected readonly ?Closure $resolveValue = null,
        protected readonly ?Closure $sortKeysUsing = null,
    ) {}

    public static function relation(string $label, string $column, string $relationship, string $model, string $titleAttribute = 'name', ?string $orderAttribute = null): self
    {
        return new self(
            label: $label,
            expression: $column,
            kind: DimensionKind::RELATION,
            groupColumn: "{$relationship}.{$titleAttribute}",
            resolveLabels: fn (array $keys): array => $model::query()
                ->withoutGlobalScopes()
                ->whereKey($keys)
                ->pluck($titleAttribute, (new $model)->getKeyName())
                ->mapWithKeys(fn (mixed $title, mixed $key): array => [(string) $key => (string) $title])
                ->all(),
            sortKeysUsing: $orderAttribute ? fn (array $keys): array => $model::query()
                ->withoutGlobalScopes()
                ->whereKey($keys)
                ->orderBy($orderAttribute)
                ->pluck((new $model)->getKeyName())
                ->map(fn (mixed $key): string => (string) $key)
                ->all() : null,
        );
    }

    public static function attribute(string $label, string $column, string $attribute): self
    {
        return new self(
            label: $label,
            expression: $column,
            kind: DimensionKind::ATTRIBUTE,
            groupColumn: $attribute,
        );
    }

    public static function options(string $label, string $column, string $attribute, array $options): self
    {
        return new self(
            label: $label,
            expression: $column,
            kind: DimensionKind::OPTIONS,
            groupColumn: $attribute,
            resolveLabels: fn (array $keys): array => $options,
        );
    }

    public static function month(string $label, string $dateExpression, string $attribute, ?Closure $resolveValue = null): self
    {
        return new self(
            label: $label,
            expression: $dateExpression,
            kind: DimensionKind::MONTH,
            groupColumn: $attribute,
            resolveValue: $resolveValue ?? fn (Model $record): mixed => data_get($record, $attribute),
        );
    }

    public function isMonth(): bool
    {
        return $this->kind === DimensionKind::MONTH;
    }

    public function getGroupColumn(): string
    {
        return (string) $this->groupColumn;
    }

    public function getKeyExpression(): string
    {
        return $this->isMonth() ? Sql::month($this->expression) : $this->expression;
    }

    public function labels(array $keys): array
    {
        $filledKeys = array_values(array_filter($keys, fn (?string $key): bool => filled($key)));

        $labels = $this->resolveLabels && $filledKeys ? ($this->resolveLabels)($filledKeys) : [];

        return array_map(fn (?string $key): string => match (true) {
            blank($key)        => static::getNoneLabel(),
            $this->isMonth()   => static::monthLabel($key),
            default            => $labels[$key] ?? $key,
        }, $keys);
    }

    public function sortKeys(array $keys): array
    {
        $blankKeys = array_values(array_filter($keys, fn (?string $key): bool => blank($key)));

        $filledKeys = array_values(array_filter($keys, fn (?string $key): bool => filled($key)));

        if ($this->isMonth()) {
            sort($filledKeys);
        } elseif ($this->sortKeysUsing) {
            $ordered = ($this->sortKeysUsing)($filledKeys);

            $filledKeys = [...array_values(array_intersect($ordered, $filledKeys)), ...array_values(array_diff($filledKeys, $ordered))];
        } else {
            $labels = array_combine($filledKeys, $this->labels($filledKeys));

            uasort($labels, fn (string $a, string $b): int => strnatcasecmp($a, $b));

            $filledKeys = array_map('strval', array_keys($labels));
        }

        return [...array_slice($blankKeys, 0, 1), ...$filledKeys];
    }

    public static function monthLabel(string $month): string
    {
        return Carbon::createFromFormat('!Y-m', $month)->translatedFormat('F Y');
    }

    public static function getNoneLabel(): string
    {
        return __('crm::filament/clusters/reporting/report.none');
    }

    public function toTableGroup(string $id): Group
    {
        $group = Group::make($id)
            ->column($this->groupColumn ?? $id)
            ->label($this->label)
            ->titlePrefixedWithLabel(false);

        if ($this->kind === DimensionKind::OPTIONS) {
            $options = ($this->resolveLabels)([]);

            return $group->getTitleFromRecordUsing(fn (Model $record): string => $options[(string) enum_value(data_get($record, $this->groupColumn))] ?? static::getNoneLabel());
        }

        if ($this->kind === DimensionKind::RELATION
            || $this->kind === DimensionKind::ATTRIBUTE) {
            return $group->getTitleFromRecordUsing(fn (Model $record): string => filled($title = data_get($record, $this->groupColumn)) ? (string) $title : static::getNoneLabel());
        }

        $monthExpression = $this->getKeyExpression();

        return $group
            ->getKeyFromRecordUsing(fn (Model $record): ?string => $this->monthKeyFromRecord($record))
            ->getTitleFromRecordUsing(fn (Model $record): string => filled($key = $this->monthKeyFromRecord($record)) ? static::monthLabel($key) : static::getNoneLabel())
            ->groupQueryUsing(fn (Builder|QueryBuilder $query): Builder|QueryBuilder => $query->groupByRaw($monthExpression))
            ->orderQueryUsing(fn (Builder|QueryBuilder $query, string $direction): Builder|QueryBuilder => $query->orderByRaw("{$this->expression} ".($direction === 'desc' ? 'desc' : 'asc')))
            ->scopeQueryByKeyUsing(fn (Builder|QueryBuilder $query, ?string $key): Builder|QueryBuilder => blank($key)
                ? $query->whereRaw("{$this->expression} is null")
                : $query->whereRaw("{$monthExpression} = ?", [$key]));
    }

    protected function monthKeyFromRecord(Model $record): ?string
    {
        $value = ($this->resolveValue)($record);

        if (blank($value)) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m');
    }
}
