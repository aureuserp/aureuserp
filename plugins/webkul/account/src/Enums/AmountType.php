<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum AmountType: string implements HasLabel
{
    case FIXED = 'fixed';

    case GROUP = 'group';

    case PERCENT = 'percent';

    case DIVISION = 'division';

    case CODE = 'code';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PERCENT  => __('accounts::enums/amount-type.percent'),
            self::FIXED    => __('accounts::enums/amount-type.fixed'),
            self::GROUP    => __('accounts::enums/amount-type.group'),
            self::DIVISION => __('accounts::enums/amount-type.division'),
            self::CODE     => __('accounts::enums/amount-type.code'),
        };
    }

    public static function options(): array
    {
        return [
            self::PERCENT->value  => __('accounts::enums/amount-type.percent'),
            self::FIXED->value    => __('accounts::enums/amount-type.fixed'),
            self::GROUP->value    => __('accounts::enums/amount-type.group'),
            self::DIVISION->value => __('accounts::enums/amount-type.division'),
            self::CODE->value     => __('accounts::enums/amount-type.code'),
        ];
    }
}
