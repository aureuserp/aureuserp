<?php

namespace Webkul\Asset\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AssetStatus: string implements HasColor, HasIcon, HasLabel
{
    case Available = 'available';

    case Held = 'held';

    case Consumed = 'consumed';

    case Scrapped = 'scrapped';

    case Lost = 'lost';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => __('assets::enums/asset-status.available'),
            self::Held      => __('assets::enums/asset-status.held'),
            self::Consumed  => __('assets::enums/asset-status.consumed'),
            self::Scrapped  => __('assets::enums/asset-status.scrapped'),
            self::Lost      => __('assets::enums/asset-status.lost'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Available => 'success',
            self::Held      => 'warning',
            self::Consumed  => 'info',
            self::Scrapped  => 'danger',
            self::Lost      => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Available => 'heroicon-m-check-circle',
            self::Held      => 'heroicon-m-user',
            self::Consumed  => 'heroicon-m-check-badge',
            self::Scrapped  => 'heroicon-m-trash',
            self::Lost      => 'heroicon-m-question-mark-circle',
        };
    }
}
