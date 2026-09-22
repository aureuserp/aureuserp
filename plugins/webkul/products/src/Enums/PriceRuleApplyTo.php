<?php

namespace Webkul\Product\Enums;

use Filament\Support\Contracts\HasLabel;

enum PriceRuleApplyTo: string implements HasLabel
{
    case VARIANT = 'variant';

    case PRODUCT = 'product';

    case CATEGORY = 'category';

    case GLOBAL = 'global';

    public function precedence(): int
    {
        return match ($this) {
            self::VARIANT  => 0,
            self::PRODUCT  => 1,
            self::CATEGORY => 2,
            self::GLOBAL   => 3,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::VARIANT   => __('products::enums/price-rule-apply-to.variant'),
            self::PRODUCT   => __('products::enums/price-rule-apply-to.product'),
            self::CATEGORY  => __('products::enums/price-rule-apply-to.category'),
            self::GLOBAL    => __('products::enums/price-rule-apply-to.global'),
        };
    }
}
