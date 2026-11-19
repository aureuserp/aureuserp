<?php

namespace Webkul\Asset\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AssetSiteUsage: string implements HasColor, HasIcon, HasLabel
{
    case AssetLocation = 'asset_location';

    case CustodyZone = 'custody_zone';

    public function getLabel(): string
    {
        return match ($this) {
            self::AssetLocation => __('assets::enums/asset-site-usage.asset-location'),
            self::CustodyZone   => __('assets::enums/asset-site-usage.custody-zone'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AssetLocation => 'info',
            self::CustodyZone   => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::AssetLocation => 'heroicon-m-building-office-2',
            self::CustodyZone   => 'heroicon-m-user-group',
        };
    }
}
