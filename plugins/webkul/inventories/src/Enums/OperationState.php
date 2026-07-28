<?php

namespace Webkul\Inventory\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OperationState: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';

    case WAITING = 'waiting';

    case CONFIRMED = 'confirmed';

    case ASSIGNED = 'assigned';

    case DONE = 'done';

    case CANCELED = 'canceled';

    public static function options(): array
    {
        return [
            self::DRAFT->value     => __('inventories::enums/operation-state.draft'),
            self::WAITING->value   => __('inventories::enums/operation-state.waiting'),
            self::CONFIRMED->value => __('inventories::enums/operation-state.confirmed'),
            self::ASSIGNED->value  => __('inventories::enums/operation-state.assigned'),
            self::DONE->value      => __('inventories::enums/operation-state.done'),
            self::CANCELED->value  => __('inventories::enums/operation-state.canceled'),
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT        => __('inventories::enums/operation-state.draft'),
            self::WAITING      => __('inventories::enums/operation-state.waiting'),
            self::CONFIRMED    => __('inventories::enums/operation-state.confirmed'),
            self::ASSIGNED     => __('inventories::enums/operation-state.assigned'),
            self::DONE         => __('inventories::enums/operation-state.done'),
            self::CANCELED     => __('inventories::enums/operation-state.canceled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT     => 'gray',
            self::WAITING   => 'warning',
            self::CONFIRMED => 'warning',
            self::ASSIGNED  => 'primary',
            self::DONE      => 'success',
            self::CANCELED  => 'danger',
        };
    }
}
