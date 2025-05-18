<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoundingStrategy: string implements HasLabel
{
    case BIGGEST_TAX = 'biggest_tax';

    case ADD_INVOICE_LINE = 'add_invoice_line';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BIGGEST_TAX      => __('accounts::enums/rounding-strategy.biggest-tax'),
            self::ADD_INVOICE_LINE => __('accounts::enums/rounding-strategy.add-invoice'),
        };
    }

    public static function options(): array
    {
        return [
            self::BIGGEST_TAX->value      => __('accounts::enums/rounding-strategy.biggest-tax'),
            self::ADD_INVOICE_LINE->value => __('accounts::enums/rounding-strategy.add-invoice'),
        ];
    }
}
