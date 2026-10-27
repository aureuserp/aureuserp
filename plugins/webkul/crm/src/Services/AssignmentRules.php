<?php

namespace Webkul\Crm\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Throwable;
use Webkul\Crm\Enums\AssignmentRuleMatch;
use Webkul\Crm\Enums\AssignmentRuleOperator;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Models\Lead;

class AssignmentRules
{
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

    public static function matches(Lead $lead, ?array $rules): bool
    {
        $rules = static::normalize($rules);

        if (! $rules['conditions']) {
            return true;
        }

        $results = array_map(fn (array $condition): bool => static::conditionMatches($lead, $condition), $rules['conditions']);

        return $rules['match'] === AssignmentRuleMatch::ANY->value
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
                    boolean: $rules['match'] === AssignmentRuleMatch::ANY->value ? 'or' : 'and',
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
            'match'      => (AssignmentRuleMatch::tryFrom((string) ($rules['match'] ?? '')) ?? AssignmentRuleMatch::ALL)->value,
            'conditions' => array_values(array_filter(array_map(static::normalizeCondition(...), $conditions))),
        ];
    }

    public static function fieldType(?string $field): ?string
    {
        return self::FIELDS[$field] ?? null;
    }

    public static function operatorsFor(?string $field): array
    {
        return AssignmentRuleOperator::forFieldType(static::fieldType($field));
    }

    protected static function normalizeCondition(array $condition): ?array
    {
        $field = $condition['field'] ?? null;
        $operator = AssignmentRuleOperator::tryFrom((string) ($condition['operator'] ?? ''));
        $type = static::fieldType($field);

        if (! $type || ! in_array($operator, AssignmentRuleOperator::forFieldType($type), true)) {
            return null;
        }

        if ($operator->isValueless()) {
            return ['field' => $field, 'operator' => $operator->value, 'value' => null];
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

        return ['field' => $field, 'operator' => $operator->value, 'value' => $value];
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
            ? array_intersect(array_map('strval', $values), array_keys(LeadType::options()))
            : array_map('intval', $values);

        return array_values(array_unique($values));
    }

    protected static function legacyConditions(array $rules): array
    {
        $conditions = [];

        foreach (['lead_types' => 'type', 'country_ids' => 'country_id', 'tag_ids' => 'tag_ids', 'source_ids' => 'utm_source_id'] as $key => $field) {
            if (filled($rules[$key] ?? null)) {
                $conditions[] = ['field' => $field, 'operator' => AssignmentRuleOperator::IN->value, 'value' => $rules[$key]];
            }
        }

        if (filled($rules['min_priority'] ?? null)) {
            $conditions[] = ['field' => 'priority', 'operator' => AssignmentRuleOperator::GREATER_THAN_OR_EQUAL->value, 'value' => $rules['min_priority']];
        }

        return $conditions;
    }

    protected static function conditionMatches(Lead $lead, array $condition): bool
    {
        ['field' => $field, 'value' => $value] = $condition;
        $operator = AssignmentRuleOperator::from($condition['operator']);

        if (static::fieldType($field) === 'tags') {
            $tagIds = $lead->tags->pluck('id')->map(fn (mixed $id): int => (int) $id);

            return match ($operator) {
                AssignmentRuleOperator::IN      => $tagIds->intersect($value)->isNotEmpty(),
                AssignmentRuleOperator::NOT_IN  => $tagIds->intersect($value)->isEmpty(),
                AssignmentRuleOperator::SET     => $tagIds->isNotEmpty(),
                AssignmentRuleOperator::NOT_SET => $tagIds->isEmpty(),
            };
        }

        $actual = $lead->getAttributes()[$field] ?? null;

        if ($operator === AssignmentRuleOperator::SET) {
            return filled($actual);
        }

        if ($operator === AssignmentRuleOperator::NOT_SET) {
            return blank($actual);
        }

        return match (static::fieldType($field)) {
            'text'   => static::textMatches($actual, $operator, $value),
            'number' => static::compare($actual === null ? null : (float) $actual, $operator, (float) $value),
            'date'   => static::compare(blank($actual) ? null : Carbon::parse($actual)->toDateString(), $operator, $value),
            'choice' => match ($operator) {
                AssignmentRuleOperator::IN     => filled($actual) && in_array((string) $actual, array_map('strval', $value), true),
                AssignmentRuleOperator::NOT_IN => blank($actual) || ! in_array((string) $actual, array_map('strval', $value), true),
            },
        };
    }

    protected static function textMatches(mixed $actual, AssignmentRuleOperator $operator, string $value): bool
    {
        $actual = mb_strtolower(trim((string) $actual));
        $value = mb_strtolower($value);

        return match ($operator) {
            AssignmentRuleOperator::CONTAINS     => str_contains($actual, $value),
            AssignmentRuleOperator::NOT_CONTAINS => ! str_contains($actual, $value),
            AssignmentRuleOperator::STARTS_WITH  => str_starts_with($actual, $value),
            AssignmentRuleOperator::EQUALS       => $actual === $value,
            AssignmentRuleOperator::NOT_EQUALS   => $actual !== $value,
        };
    }

    protected static function compare(float|string|null $actual, AssignmentRuleOperator $operator, float|string $value): bool
    {
        if ($actual === null) {
            return $operator === AssignmentRuleOperator::NOT_EQUALS;
        }

        return match ($operator) {
            AssignmentRuleOperator::EQUALS                => $actual == $value,
            AssignmentRuleOperator::NOT_EQUALS            => $actual != $value,
            AssignmentRuleOperator::GREATER_THAN          => $actual > $value,
            AssignmentRuleOperator::GREATER_THAN_OR_EQUAL => $actual >= $value,
            AssignmentRuleOperator::LESS_THAN             => $actual < $value,
            AssignmentRuleOperator::LESS_THAN_OR_EQUAL    => $actual <= $value,
        };
    }

    protected static function applyCondition(Builder $query, array $condition): void
    {
        ['field' => $field, 'value' => $value] = $condition;
        $operator = AssignmentRuleOperator::from($condition['operator']);

        if (static::fieldType($field) === 'tags') {
            $inTags = fn (Builder $query): Builder => $query->whereIn('crm_tags.id', $value);

            match ($operator) {
                AssignmentRuleOperator::IN      => $query->whereHas('tags', $inTags),
                AssignmentRuleOperator::NOT_IN  => $query->whereDoesntHave('tags', $inTags),
                AssignmentRuleOperator::SET     => $query->has('tags'),
                AssignmentRuleOperator::NOT_SET => $query->doesntHave('tags'),
            };

            return;
        }

        $column = $query->getModel()->qualifyColumn($field);

        if ($operator === AssignmentRuleOperator::SET) {
            $query->whereNotNull($column);

            if (static::fieldType($field) === 'text') {
                $query->where($column, '!=', '');
            }

            return;
        }

        if ($operator === AssignmentRuleOperator::NOT_SET) {
            $query->where(fn (Builder $query): Builder => static::fieldType($field) === 'text'
                ? $query->whereNull($column)->orWhere($column, '')
                : $query->whereNull($column));

            return;
        }

        $like = fn (string $pattern): string => str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $pattern);

        match (static::fieldType($field)) {
            'text' => match ($operator) {
                AssignmentRuleOperator::CONTAINS     => $query->where($column, 'like', '%'.$like($value).'%'),
                AssignmentRuleOperator::NOT_CONTAINS => $query->where(fn (Builder $query): Builder => $query->whereNull($column)->orWhere($column, 'not like', '%'.$like($value).'%')),
                AssignmentRuleOperator::STARTS_WITH  => $query->where($column, 'like', $like($value).'%'),
                AssignmentRuleOperator::EQUALS       => $query->where($column, $value),
                AssignmentRuleOperator::NOT_EQUALS   => $query->where(fn (Builder $query): Builder => $query->whereNull($column)->orWhere($column, '!=', $value)),
            },
            'number' => $operator === AssignmentRuleOperator::NOT_EQUALS
                ? $query->where(fn (Builder $query): Builder => $query->whereNull($column)->orWhere($column, '!=', $value))
                : $query->where($column, $operator->value, $value),
            'date'   => $query->whereDate($column, $operator->value, $value),
            'choice' => match ($operator) {
                AssignmentRuleOperator::IN     => $query->whereIn($column, $value),
                AssignmentRuleOperator::NOT_IN => $query->where(fn (Builder $query): Builder => $query->whereNull($column)->orWhereNotIn($column, $value)),
            },
        };
    }
}
