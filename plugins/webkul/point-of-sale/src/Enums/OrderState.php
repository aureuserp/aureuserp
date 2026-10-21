<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderState: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';

    case PAID = 'paid';

    case DONE = 'done';

    case INVOICED = 'invoiced';

    case CANCELED = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT    => __('point-of-sale::enums/order-state.draft'),
            self::PAID     => __('point-of-sale::enums/order-state.paid'),
            self::DONE     => __('point-of-sale::enums/order-state.done'),
            self::INVOICED => __('point-of-sale::enums/order-state.invoiced'),
            self::CANCELED => __('point-of-sale::enums/order-state.canceled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT    => 'gray',
            self::PAID     => 'success',
            self::DONE     => 'primary',
            self::INVOICED => 'info',
            self::CANCELED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT    => 'heroicon-o-pencil-square',
            self::PAID     => 'heroicon-o-banknotes',
            self::DONE     => 'heroicon-o-check-circle',
            self::INVOICED => 'heroicon-o-document-text',
            self::CANCELED => 'heroicon-o-x-circle',
        };
    }

    public function isSettled(): bool
    {
        return in_array($this, [self::PAID, self::DONE, self::INVOICED], true);
    }
}
