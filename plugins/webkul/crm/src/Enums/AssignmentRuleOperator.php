<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssignmentRuleOperator: string implements HasLabel
{
    case CONTAINS = 'contains';

    case NOT_CONTAINS = 'not_contains';

    case EQUALS = '=';

    case NOT_EQUALS = '!=';

    case STARTS_WITH = 'starts_with';

    case GREATER_THAN = '>';

    case GREATER_THAN_OR_EQUAL = '>=';

    case LESS_THAN = '<';

    case LESS_THAN_OR_EQUAL = '<=';

    case IN = 'in';

    case NOT_IN = 'not_in';

    case SET = 'set';

    case NOT_SET = 'not_set';

    public static function forFieldType(?string $type): array
    {
        return match ($type) {
            'text'           => [self::CONTAINS, self::NOT_CONTAINS, self::EQUALS, self::NOT_EQUALS, self::STARTS_WITH, self::SET, self::NOT_SET],
            'number'         => [self::EQUALS, self::NOT_EQUALS, self::GREATER_THAN, self::GREATER_THAN_OR_EQUAL, self::LESS_THAN, self::LESS_THAN_OR_EQUAL],
            'choice', 'tags' => [self::IN, self::NOT_IN, self::SET, self::NOT_SET],
            'date'           => [self::GREATER_THAN_OR_EQUAL, self::LESS_THAN_OR_EQUAL, self::SET, self::NOT_SET],
            default          => [],
        };
    }

    public function isValueless(): bool
    {
        return $this === self::SET || $this === self::NOT_SET;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::CONTAINS              => __('crm::enums/assignment-rule-operator.contains'),
            self::NOT_CONTAINS          => __('crm::enums/assignment-rule-operator.not-contains'),
            self::EQUALS                => __('crm::enums/assignment-rule-operator.equals'),
            self::NOT_EQUALS            => __('crm::enums/assignment-rule-operator.not-equals'),
            self::STARTS_WITH           => __('crm::enums/assignment-rule-operator.starts-with'),
            self::GREATER_THAN          => __('crm::enums/assignment-rule-operator.greater-than'),
            self::GREATER_THAN_OR_EQUAL => __('crm::enums/assignment-rule-operator.greater-than-or-equal'),
            self::LESS_THAN             => __('crm::enums/assignment-rule-operator.less-than'),
            self::LESS_THAN_OR_EQUAL    => __('crm::enums/assignment-rule-operator.less-than-or-equal'),
            self::IN                    => __('crm::enums/assignment-rule-operator.in'),
            self::NOT_IN                => __('crm::enums/assignment-rule-operator.not-in'),
            self::SET                   => __('crm::enums/assignment-rule-operator.set'),
            self::NOT_SET               => __('crm::enums/assignment-rule-operator.not-set'),
        };
    }

    public function getDateLabel(): string
    {
        return match ($this) {
            self::GREATER_THAN_OR_EQUAL => __('crm::enums/assignment-rule-operator.date.greater-than-or-equal'),
            self::LESS_THAN_OR_EQUAL    => __('crm::enums/assignment-rule-operator.date.less-than-or-equal'),
            self::SET                   => __('crm::enums/assignment-rule-operator.date.set'),
            self::NOT_SET               => __('crm::enums/assignment-rule-operator.date.not-set'),
            default                     => $this->getLabel(),
        };
    }
}
