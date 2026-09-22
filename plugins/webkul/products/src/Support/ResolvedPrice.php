<?php

namespace Webkul\Product\Support;

use Webkul\Product\Models\PriceRuleItem;

class ResolvedPrice
{
    public function __construct(
        public readonly float $price,
        public readonly ?PriceRuleItem $rule = null,
        public readonly ?float $basePrice = null,
    ) {}
}
