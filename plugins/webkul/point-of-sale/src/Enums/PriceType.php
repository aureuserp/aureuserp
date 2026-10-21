<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PriceType: string implements HasColor, HasLabel
{
    case ORIGINAL = 'original';

    case MANUAL = 'manual';

    case AUTOMATIC = 'automatic';

    public function getLabel(): string
    {
        return match ($this) {
            self::ORIGINAL  => __('point-of-sale::enums/price-type.original'),
            self::MANUAL    => __('point-of-sale::enums/price-type.manual'),
            self::AUTOMATIC => __('point-of-sale::enums/price-type.automatic'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ORIGINAL  => 'gray',
            self::MANUAL    => 'warning',
            self::AUTOMATIC => 'primary',
        };
    }
}
