<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ManufacturingOrderReservationState: string implements HasColor, HasLabel
{
    case CONFIRMED = 'confirmed';
    case ASSIGNED = 'assigned';
    case WAITING = 'waiting';

    public static function options(): array
    {
        return [
            self::CONFIRMED->value => __('manufacturing::enums/manufacturing-order-reservation-state.confirmed'),
            self::ASSIGNED->value  => __('manufacturing::enums/manufacturing-order-reservation-state.assigned'),
            self::WAITING->value   => __('manufacturing::enums/manufacturing-order-reservation-state.waiting'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::CONFIRMED => 'warning',
            self::ASSIGNED  => 'success',
            self::WAITING   => 'gray',
        };
    }
}
