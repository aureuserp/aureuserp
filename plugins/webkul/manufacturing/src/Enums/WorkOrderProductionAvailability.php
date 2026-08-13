<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum WorkOrderProductionAvailability: string implements HasColor, HasLabel
{
    case CONFIRMED = 'confirmed';

    case ASSIGNED = 'assigned';

    case WAITING = 'waiting';

    public static function options(): array
    {
        return [
            self::CONFIRMED->value => __('manufacturing::enums/work-order-production-availability.confirmed'),
            self::ASSIGNED->value  => __('manufacturing::enums/work-order-production-availability.assigned'),
            self::WAITING->value   => __('manufacturing::enums/work-order-production-availability.waiting'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return 'success';
    }
}
