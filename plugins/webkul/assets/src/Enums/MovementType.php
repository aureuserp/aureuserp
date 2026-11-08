<?php

namespace Webkul\Asset\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MovementType: string implements HasColor, HasIcon, HasLabel
{
    case Registered = 'registered';

    case Issued = 'issued';

    case Transferred = 'transferred';

    case Returned = 'returned';

    case Scrapped = 'scrapped';

    case Lost = 'lost';

    public function getLabel(): string
    {
        return match ($this) {
            self::Registered  => __('assets::enums/movement-type.registered'),
            self::Issued      => __('assets::enums/movement-type.issued'),
            self::Transferred => __('assets::enums/movement-type.transferred'),
            self::Returned    => __('assets::enums/movement-type.returned'),
            self::Scrapped    => __('assets::enums/movement-type.scrapped'),
            self::Lost        => __('assets::enums/movement-type.lost'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Registered  => 'primary',
            self::Issued      => 'warning',
            self::Transferred => 'info',
            self::Returned    => 'success',
            self::Scrapped    => 'danger',
            self::Lost        => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Registered  => 'heroicon-m-plus-circle',
            self::Issued      => 'heroicon-m-arrow-right-circle',
            self::Transferred => 'heroicon-m-arrows-right-left',
            self::Returned    => 'heroicon-m-arrow-uturn-left',
            self::Scrapped    => 'heroicon-m-trash',
            self::Lost        => 'heroicon-m-exclamation-triangle',
        };
    }
}
