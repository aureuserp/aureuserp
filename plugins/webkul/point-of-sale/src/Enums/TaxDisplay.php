<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasLabel;

enum TaxDisplay: string implements HasLabel
{
    case SUBTOTAL = 'subtotal';

    case TOTAL = 'total';

    public function getLabel(): string
    {
        return match ($this) {
            self::SUBTOTAL => __('point-of-sale::enums/tax-display.subtotal'),
            self::TOTAL    => __('point-of-sale::enums/tax-display.total'),
        };
    }
}
