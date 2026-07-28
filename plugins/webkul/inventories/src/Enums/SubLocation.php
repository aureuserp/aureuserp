<?php

namespace Webkul\Inventory\Enums;

use Filament\Support\Contracts\HasLabel;

enum SubLocation: string implements HasLabel
{
    case NO = 'no';

    case LAST_USED = 'last_used';

    case CLOSEST_LOCATION = 'closest_location';

    public function getLabel(): string
    {
        return match ($this) {
            self::NO               => __('inventories::enums/sub-location.no'),
            self::LAST_USED        => __('inventories::enums/sub-location.last-used'),
            self::CLOSEST_LOCATION => __('inventories::enums/sub-location.closest-location'),
        };
    }
}
