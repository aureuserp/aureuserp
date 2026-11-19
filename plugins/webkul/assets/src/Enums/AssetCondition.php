<?php

namespace Webkul\Asset\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AssetCondition: string implements HasColor, HasIcon, HasLabel
{
    case New = 'new';

    case Good = 'good';

    case Fair = 'fair';

    case Poor = 'poor';

    case Damaged = 'damaged';

    public function getLabel(): string
    {
        return match ($this) {
            self::New     => __('assets::enums/asset-condition.new'),
            self::Good    => __('assets::enums/asset-condition.good'),
            self::Fair    => __('assets::enums/asset-condition.fair'),
            self::Poor    => __('assets::enums/asset-condition.poor'),
            self::Damaged => __('assets::enums/asset-condition.damaged'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New     => 'success',
            self::Good    => 'success',
            self::Fair    => 'warning',
            self::Poor    => 'danger',
            self::Damaged => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::New     => 'heroicon-m-sparkles',
            self::Good    => 'heroicon-m-check-circle',
            self::Fair    => 'heroicon-m-minus-circle',
            self::Poor    => 'heroicon-m-exclamation-triangle',
            self::Damaged => 'heroicon-m-x-circle',
        };
    }
}
