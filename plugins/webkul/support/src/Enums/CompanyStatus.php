<?php

namespace Webkul\Support\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CompanyStatus: string implements HasColor, HasIcon, HasLabel
{
    case ACTIVE = 'active';

    case INACTIVE = 'inactive';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE   => __('support::enums/company-status.active'),
            self::INACTIVE => __('support::enums/company-status.inactive'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::ACTIVE   => 'success',
            self::INACTIVE => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ACTIVE   => 'heroicon-o-check-circle',
            self::INACTIVE => 'heroicon-o-x-circle',
        };
    }
}
