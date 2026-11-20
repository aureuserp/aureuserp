<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OpportunityPriority: int implements HasColor, HasLabel
{
    case LOW = 0;

    case MEDIUM = 1;

    case HIGH = 2;

    case VERY_HIGH = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW       => __('crm::enums/opportunity-priority.low'),
            self::MEDIUM    => __('crm::enums/opportunity-priority.medium'),
            self::HIGH      => __('crm::enums/opportunity-priority.high'),
            self::VERY_HIGH => __('crm::enums/opportunity-priority.very-high'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::LOW       => 'gray',
            self::MEDIUM    => 'info',
            self::HIGH      => 'warning',
            self::VERY_HIGH => 'danger',
        };
    }
}
