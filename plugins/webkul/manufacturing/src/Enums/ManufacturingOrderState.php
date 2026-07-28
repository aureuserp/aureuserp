<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ManufacturingOrderState: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case PROGRESS = 'progress';
    case TO_CLOSE = 'to_close';
    case DONE = 'done';
    case CANCEL = 'cancel';

    public static function options(): array
    {
        return [
            self::DRAFT->value     => __('manufacturing::enums/manufacturing-order-state.draft'),
            self::CONFIRMED->value => __('manufacturing::enums/manufacturing-order-state.confirmed'),
            self::PROGRESS->value  => __('manufacturing::enums/manufacturing-order-state.progress'),
            self::TO_CLOSE->value  => __('manufacturing::enums/manufacturing-order-state.to-close'),
            self::DONE->value      => __('manufacturing::enums/manufacturing-order-state.done'),
            self::CANCEL->value    => __('manufacturing::enums/manufacturing-order-state.cancel'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT     => 'gray',
            self::CONFIRMED => 'warning',
            self::PROGRESS  => 'primary',
            self::TO_CLOSE  => 'info',
            self::DONE      => 'success',
            self::CANCEL    => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT     => 'heroicon-o-document',
            self::CONFIRMED => 'heroicon-o-check-circle',
            self::PROGRESS  => 'heroicon-o-play-circle',
            self::TO_CLOSE  => 'heroicon-o-clock',
            self::DONE      => 'heroicon-o-check-badge',
            self::CANCEL    => 'heroicon-o-x-circle',
        };
    }
}
