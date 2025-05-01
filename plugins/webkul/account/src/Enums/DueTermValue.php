<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum DueTermValue: string implements HasLabel
{
    case PERCENT = 'percent';

    case FIXED = 'fixed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PERCENT => __('accounts::enums/due-term-value.percent'),
            self::FIXED   => __('accounts::enums/due-term-value.fixed'),
        };
    }

    public static function options(): array
    {
        return [
            self::PERCENT->value => __('accounts::enums/due-term-value.percent'),
            self::FIXED->value   => __('accounts::enums/due-term-value.fixed'),
        ];
    }
}
