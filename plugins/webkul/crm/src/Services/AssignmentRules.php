<?php

namespace Webkul\Crm\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Throwable;
use Webkul\Crm\Models\Lead;

class AssignmentRules
{
    public const MATCH_ALL = 'all';

    public const MATCH_ANY = 'any';

    public const FIELDS = [
        'type'                  => 'choice',
        'name'                  => 'text',
        'contact_name'          => 'text',
        'company_name'          => 'text',
        'job_title'             => 'text',
        'email'                 => 'text',
        'phone'                 => 'text',
        'website'               => 'text',
        'street1'               => 'text',
        'city'                  => 'text',
        'zip'                   => 'text',
        'state_id'              => 'choice',
        'country_id'            => 'choice',
        'stage_id'              => 'choice',
        'tag_ids'               => 'tags',
        'priority'              => 'number',
        'probability'           => 'number',
        'expected_revenue'      => 'number',
        'expected_closing_date' => 'date',
        'created_at'            => 'date',
        'utm_campaign_id'       => 'choice',
        'utm_medium_id'         => 'choice',
        'utm_source_id'         => 'choice',
        'referred_by'           => 'text',
    ];

    public const OPERATORS = [
        'text'   => ['contains', 'not_contains', '=', '!=', 'starts_with', 'set', 'not_set'],
        'number' => ['=', '!=', '>', '>=', '<', '<='],
        'choice' => ['in', 'not_in', 'set', 'not_set'],
        'tags'   => ['in', 'not_in', 'set', 'not_set'],
        'date'   => ['>=', '<=', 'set', 'not_set'],
    ];

    public const VALUELESS_OPERATORS = ['set', 'not_set'];

    public static function matches(Lead $lead, ?array $rules): bool
    {
        $rules = static::normalize($rules);

        if (! $rules['conditions']) {
            return true;
        }

        $results = array_map(fn (array $condition): bool => static::conditionMatches($lead, $condition), $rules['conditions']);

        return $rules['match'] === self::MATCH_ANY
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);
    }

    public static function apply(Builder $query, ?array $rules): Builder
    {
        $rules = static::normalize($rules);

        if (! $rules['conditions']) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($rules): void {
            foreach ($rules['conditions'] as $condition) {
                $query->where(
                    fn (Builder $query) => static::applyCondition($query, $condition),
                    boolean: $rules['match'] === self::MATCH_ANY ? 'or' : 'and',
                );
            }
        });
    }

    public static function clean(?array $rules): ?array
    {
        $rules = static::normalize($rules);

        return $rules['conditions'] ? $rules : null;
    }

    public static function normalize(?array $rules): array
    {
        $rules ??= [];

        $conditions = [
            ...array_values(array_filter((array) ($rules['conditions'] ?? []), 'is_array')),
            ...static::legacyConditions($rules),
        ];

        return [
            'match'      => ($rules['match'] ?? null) === self::MATCH_ANY ? self::MATCH_ANY : self::MATCH_ALL,
            'conditions' => array_values(array_filter(array_map(static::normalizeCondition(...), $conditions))),
        ];
    }

    public static function fieldType(?string $field): ?string
    {
        return self::FIELDS[$field] ?? null;
    }

    public static function operatorsFor(?string $field): array
    {
        return self::OPERATORS[static::fieldType($field)] ?? [];
    }

    protected static function normalizeCondition(array $condition): ?array
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? null;
        $type = static::fieldType($field);

        if (! $type || ! in_array($operator, self::OPERATORS[$type], true)) {
            return null;
        }

        if (in_array($operator, self::VALUELESS_OPERATORS, true)) {
            return ['field' => $field, 'operator' => $operator, 'value' => null];
        }

        $value = $condition['value'] ?? null;

        $value = match ($type) {
            'text'           => is_scalar($value) ? trim((string) $value) : null,
            'number'         => is_numeric($value) ? $value + 0 : null,
            'date'           => static::normalizeDate($value),
            'choice', 'tags' => static::normalizeChoices($field, $value),
        };

        if (blank($value)) {
            return null;
        }

        return ['field' => $field, 'operator' => $operator, 'value' => $value];
    }

    protected static function normalizeDate(mixed $value): ?string
    {
        if (blank($value) || ! is_string($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (Throwable) {
            return null;
        }
    }

    protected static function normalizeChoices(string $field, mixed $value): array
    {
        $values = array_filter((array) $value, fn (mixed $item): bool => is_scalar($item) && filled($item));

        $values = $field === 'type'
            ? array_intersect(array_map('strval', $values), [Lead::TYPE_LEAD, Lead::TYPE_OPPORTUNITY])
            : array_map('intval', $values);

        return array_values(array_unique($values));
    }

    protected static function legacyConditions(array $rules): array
    {
        $conditions = [];

        foreach (['lead_types' => 'type', 'country_ids' => 'country_id', 'tag_ids' => 'tag_ids', 'source_ids' => 'utm_source_id'] as $key => $field) {
            if (filled($rules[$key] ?? null)) {
                $conditions[] = ['field' => $field, 'operator' => 'in', 'value' => $rules[$key]];
            }
        }

        if (filled($rules['min_priority'] ?? null)) {
            $conditions[] = ['field' => 'priority', 'operator' => '>=', 'value' => $rules['min_priority']];
        }

        return $conditions;
    }

    protected static function conditionMatches(Lead $lead, array $condition): bool
    {
        ['field' => $field, 'operator' => $operator, 'value' => $value] = $condition;

        if (static::fieldType($field) === 'tags') {
            $tagIds = $lead->tags->pluck('id')->map(fn (mixed $id): int => (int) $id);

            return match ($operator) {
                'in'      => $tagIds->intersect($value)->isNotEmpty(),
                'not_in'  => $tagIds->intersect($value)->isEmpty(),
                'set'     => $tagIds->isNotEmpty(),
                'not_set' => $tagIds->isEmpty(),
            };
        }

        $actual = $lead->getAttributes()[$field] ?? null;

        if ($operator === 'set') {
            return filled($actual);
        }

        if ($operator === 'not_set') {
            return blank($actual);
        }

        return match (static::fieldType($field)) {
            'text'   => static::textMatches($actual, $operator, $value),
            'number' => static::compare($actual === null ? null : (float) $actual, $operator, (float) $value),
            'date'   => static::compare(blank($actual) ? null : Carbon::parse($actual)->toDateString(), $operator, $value),
            'choice' => match ($operator) {
                'in'     => filled($actual) && in_array((string) $actual, array_map('strval', $value), true),
                'not_in' => blank($actual) || ! in_array((string) $actual, array_map('strval', $value), true),
            },
        };
    }

    protected static function textMatches(mixed $actual, string $operator, string $value): bool
    {
        $actual = mb_strtolower(trim((string) $actual));
        $value = mb_strtolower($value);

        return match ($operator) {
            'contains'     => str_contains($actual, $value),
            'not_contains' => ! str_contains($actual, $value),
            'starts_with'  => str_starts_with($actual, $value),
            '='            => $actual === $value,
            '!='           => $actual !== $value,
        };
    }

    protected static function compare(float|string|null $actual, string $operator, float|string $value): bool
    {
        if ($actual === null) {
            return $operator === '!=';
        }

        return match ($operator) {
            '='  => $actual == $value,
            '!=' => $actual != $value,
            '>'  => $actual > $value,
            '>=' => $actual >= $value,
            '<'  => $actual < $value,
            '<=' => $actual <= $value,
        };
    }

    protected static function applyCondition(Builder $query, array $condition): void
    {
        ['field' => $field, 'operator' => $operator, 'value' => $value] = $condition;

        if (static::fieldType($field) === 'tags') {
            $inTags = fn (Builder $query): Builder => $query->whereIn('crm_tags.id', $value);

            match ($operator) {
                'in'      => $query->whereHas('tags', $inTags),
                'not_in'  => $query->whereDoesntHave('tags', $inTags),
                'set'     => $query->has('tags'),
                'not_set' => $query->doesntHave('tags'),
            };

            return;
        }

        $column = $query->getModel()->qualifyColumn($field);

        if ($operator === 'set') {
            $query->whereNotNull($column);

            if (static::fieldType($field) === 'text') {
                $query->where($column, '!=', '');
            }

            return;
        }

        if ($operator === 'not_set') {
            $query->where(fn (Builder $query): Builder => static::fieldType($field) === 'text'
                ? $query->whereNull($column)->orWhere($column, '')
                : $query->whereNull($column));

            return;
        }

        $like = fn (string $pattern): string => str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $pattern);

        match (static::fieldType($field)) {
            'text' => match ($operator) {
                'contains'     => $query->where($column, 'like', '%'.$like($value).'%'),
                'not_contains' => $query->where(fn (Builder $query): Builder => $query->whereNull($column)->orWhere($column, 'not like', '%'.$like($value).'%')),
                'starts_with'  => $query->where($column, 'like', $like($value).'%'),
                '='            => $query->where($column, $value),
                '!='           => $query->where(fn (Builder $query): Builder => $query->whereNull($column)->orWhere($column, '!=', $value)),
            },
            'number' => $operator === '!='
                ? $query->where(fn (Builder $query): Builder => $query->whereNull($column)->orWhere($column, '!=', $value))
                : $query->where($column, $operator, $value),
            'date'   => $query->whereDate($column, $operator, $value),
            'choice' => match ($operator) {
                'in'     => $query->whereIn($column, $value),
                'not_in' => $query->where(fn (Builder $query): Builder => $query->whereNull($column)->orWhereNotIn($column, $value)),
            },
        };
    }
}
