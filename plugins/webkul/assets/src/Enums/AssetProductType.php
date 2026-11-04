<?php

namespace Webkul\Asset\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AssetProductType: string implements HasColor, HasIcon, HasLabel
{
    case Asset = 'asset';

    case Consumable = 'consumable';

    public function getLabel(): string
    {
        return match ($this) {
            self::Asset      => __('assets::enums/asset-product-type.asset'),
            self::Consumable => __('assets::enums/asset-product-type.consumable'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Asset      => 'info',
            self::Consumable => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Asset      => 'heroicon-m-cube',
            self::Consumable => 'heroicon-m-archive-box',
        };
    }
}
