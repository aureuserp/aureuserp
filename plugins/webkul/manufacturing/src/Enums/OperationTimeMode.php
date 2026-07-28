<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OperationTimeMode: string implements HasColor, HasLabel
{
    case AUTO = 'auto';
    case MANUAL = 'manual';

    public static function options(): array
    {
        return [
            self::AUTO->value   => __('manufacturing::enums/operation-time-mode.auto'),
            self::MANUAL->value => __('manufacturing::enums/operation-time-mode.manual'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AUTO   => 'primary',
            self::MANUAL => 'gray',
        };
    }
}
