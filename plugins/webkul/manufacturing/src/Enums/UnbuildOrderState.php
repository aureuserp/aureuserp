<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UnbuildOrderState: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';
    case DONE = 'done';

    public static function options(): array
    {
        return [
            self::DRAFT->value => __('manufacturing::enums/unbuild-order-state.draft'),
            self::DONE->value  => __('manufacturing::enums/unbuild-order-state.done'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::DONE  => 'success',
        };
    }
}
