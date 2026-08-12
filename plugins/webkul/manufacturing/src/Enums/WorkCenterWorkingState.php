<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum WorkCenterWorkingState: string implements HasColor, HasLabel
{
    case NORMAL = 'normal';
    case BLOCKED = 'blocked';
    case DONE = 'done';

    public static function options(): array
    {
        return [
            self::NORMAL->value  => __('manufacturing::enums/work-center-working-state.normal'),
            self::BLOCKED->value => __('manufacturing::enums/work-center-working-state.blocked'),
            self::DONE->value    => __('manufacturing::enums/work-center-working-state.done'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NORMAL  => 'gray',
            self::BLOCKED => 'danger',
            self::DONE    => 'primary',
        };
    }
}
