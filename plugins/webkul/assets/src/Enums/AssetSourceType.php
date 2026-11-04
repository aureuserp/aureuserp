<?php

namespace Webkul\Asset\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AssetSourceType: string implements HasColor, HasIcon, HasLabel
{
    case Manual = 'manual';

    case Bulk = 'bulk';

    case Purchase = 'purchase';

    public function getLabel(): string
    {
        return match ($this) {
            self::Manual   => __('assets::enums/asset-source-type.manual'),
            self::Bulk     => __('assets::enums/asset-source-type.bulk'),
            self::Purchase => __('assets::enums/asset-source-type.purchase'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Manual   => 'gray',
            self::Bulk     => 'info',
            self::Purchase => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Manual   => 'heroicon-m-pencil-square',
            self::Bulk     => 'heroicon-m-queue-list',
            self::Purchase => 'heroicon-m-shopping-cart',
        };
    }
}
