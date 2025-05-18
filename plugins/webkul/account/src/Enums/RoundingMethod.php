<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoundingMethod: string implements HasLabel
{
    case UP = 'up';

    case DOWN = 'down';

    case HALF_UP = 'half_up';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UP      => __('accounts::enums/rounding-method.up'),
            self::DOWN    => __('accounts::enums/rounding-method.down'),
            self::HALF_UP => __('accounts::enums/rounding-method.half-up'),
        };
    }

    public static function options(): array
    {
        return [
            self::UP->value      => __('accounts::enums/rounding-method.up'),
            self::DOWN->value    => __('accounts::enums/rounding-method.down'),
            self::HALF_UP->value => __('accounts::enums/rounding-method.half-up'),
        ];
    }
}
