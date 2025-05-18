<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum TypeTaxUse: string implements HasLabel
{
    case SALE = 'sale';

    case PURCHASE = 'purchase';

    case NONE = 'none';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SALE     => __('accounts::enums/type-tax-use.sale'),
            self::PURCHASE => __('accounts::enums/type-tax-use.purchase'),
            self::NONE     => __('accounts::enums/type-tax-use.none'),
        };
    }

    public static function options(): array
    {
        return [
            self::SALE->value     => __('accounts::enums/type-tax-use.sale'),
            self::PURCHASE->value => __('accounts::enums/type-tax-use.purchase'),
            self::NONE->value     => __('accounts::enums/type-tax-use.none'),
        ];
    }
}
