<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum Applicability: string implements HasLabel
{
    case ACCOUNT = 'percent';

    case TAXES = 'taxes';

    case PRODUCTS = 'products';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACCOUNT  => __('accounts::enums/applicability.account'),
            self::TAXES    => __('accounts::enums/applicability.taxes'),
            self::PRODUCTS => __('accounts::enums/applicability.products'),
        };
    }

    public static function options(): array
    {
        return [
            self::ACCOUNT->value  => __('accounts::enums/applicability.account'),
            self::TAXES->value    => __('accounts::enums/applicability.taxes'),
            self::PRODUCTS->value => __('accounts::enums/applicability.products'),
        ];
    }
}
