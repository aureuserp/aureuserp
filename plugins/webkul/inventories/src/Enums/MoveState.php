<?php

namespace Webkul\Inventory\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MoveState: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';

    case CONFIRMED = 'confirmed';

    case WAITING = 'waiting';

    case ASSIGNED = 'assigned';

    case PARTIALLY_ASSIGNED = 'partially_assigned';

    case DONE = 'done';

    case CANCELED = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT              => __('inventories::enums/move-state.draft'),
            self::CONFIRMED          => __('inventories::enums/move-state.confirmed'),
            self::WAITING            => __('inventories::enums/move-state.waiting'),
            self::ASSIGNED           => __('inventories::enums/move-state.assigned'),
            self::PARTIALLY_ASSIGNED => __('inventories::enums/move-state.partially-assigned'),
            self::DONE               => __('inventories::enums/move-state.done'),
            self::CANCELED           => __('inventories::enums/move-state.canceled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT              => 'gray',
            self::CONFIRMED          => 'warning',
            self::WAITING            => 'warning',
            self::ASSIGNED           => 'primary',
            self::PARTIALLY_ASSIGNED => 'primary',
            self::DONE               => 'success',
            self::CANCELED           => 'danger',
        };
    }
}
