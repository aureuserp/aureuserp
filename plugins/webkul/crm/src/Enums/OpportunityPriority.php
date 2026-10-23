<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OpportunityPriority: int implements HasColor, HasLabel
{
    case Low = 0;

    case Medium = 1;

    case High = 2;

    case VeryHigh = 3;

    public function getLabel(): string
    {
        return __('crm::enums/opportunity-priority.'.$this->name);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Low      => 'gray',
            self::Medium   => 'info',
            self::High     => 'warning',
            self::VeryHigh => 'danger',
        };
    }
}
