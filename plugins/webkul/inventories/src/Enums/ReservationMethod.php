<?php

namespace Webkul\Inventory\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReservationMethod: string implements HasLabel
{
    case AT_CONFIRM = 'at_confirm';

    case MANUAL = 'manual';

    // case BY_DATE = 'by_date';

    public function getLabel(): string
    {
        return match ($this) {
            self::AT_CONFIRM => __('inventories::enums/reservation-method.at-confirm'),
            self::MANUAL     => __('inventories::enums/reservation-method.manual'),
            // self::BY_DATE    => __('inventories::enums/reservation-method.by-date'),
        };
    }
}
