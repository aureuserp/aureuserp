<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadScoringVariable: string implements HasLabel
{
    case COUNTRY = 'country_id';

    case STATE = 'state_id';

    case PHONE = 'phone';

    case EMAIL = 'email';

    case SOURCE = 'utm_source_id';

    case TAGS = 'tags';

    case STAGE = 'stage';

    public function getLabel(): string
    {
        return match ($this) {
            self::COUNTRY => __('crm::enums/lead-scoring-variable.country'),
            self::STATE   => __('crm::enums/lead-scoring-variable.state'),
            self::PHONE   => __('crm::enums/lead-scoring-variable.phone'),
            self::EMAIL   => __('crm::enums/lead-scoring-variable.email'),
            self::SOURCE  => __('crm::enums/lead-scoring-variable.source'),
            self::TAGS    => __('crm::enums/lead-scoring-variable.tags'),
            self::STAGE   => __('crm::enums/lead-scoring-variable.stage'),
        };
    }

    public static function selectable(): array
    {
        return array_values(array_filter(self::cases(), fn (self $variable): bool => $variable !== self::STAGE));
    }

    public static function selectableValues(): array
    {
        return array_map(fn (self $variable): string => $variable->value, self::selectable());
    }

    public static function options(): array
    {
        return array_combine(self::selectableValues(), array_map(fn (self $variable): string => $variable->getLabel(), self::selectable()));
    }
}
