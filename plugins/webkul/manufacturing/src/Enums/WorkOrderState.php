<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WorkOrderState: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';
    case WAITING = 'waiting';
    case READY = 'ready';
    case PROGRESS = 'progress';
    case DONE = 'done';
    case CANCEL = 'cancel';

    public static function options(): array
    {
        return [
            self::PENDING->value  => __('manufacturing::enums/work-order-state.pending'),
            self::WAITING->value  => __('manufacturing::enums/work-order-state.waiting'),
            self::READY->value    => __('manufacturing::enums/work-order-state.ready'),
            self::PROGRESS->value => __('manufacturing::enums/work-order-state.progress'),
            self::DONE->value     => __('manufacturing::enums/work-order-state.done'),
            self::CANCEL->value   => __('manufacturing::enums/work-order-state.cancel'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING  => 'gray',
            self::WAITING  => 'warning',
            self::READY    => 'success',
            self::PROGRESS => 'primary',
            self::DONE     => 'success',
            self::CANCEL   => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING  => 'heroicon-o-pause-circle',
            self::WAITING  => 'heroicon-o-clock',
            self::READY    => 'heroicon-o-check-circle',
            self::PROGRESS => 'heroicon-o-play-circle',
            self::DONE     => 'heroicon-o-check-badge',
            self::CANCEL   => 'heroicon-o-x-circle',
        };
    }
}
