<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum DisplayType: string implements HasLabel
{
    case PRODUCT = 'product';

    case COGS = 'cogs';

    case TAX = 'tax';

    case DISCOUNT = 'discount';

    case ROUNDING = 'rounding';

    case PAYMENT_TERM = 'payment_term';

    case LINE_SECTION = 'line_section';

    case LINE_NOTE = 'line_note';

    case EPD = 'epd';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PRODUCT      => __('accounts::enums/display-type.product'),
            self::COGS         => __('accounts::enums/display-type.cogs'),
            self::TAX          => __('accounts::enums/display-type.tax'),
            self::DISCOUNT     => __('accounts::enums/display-type.discount'),
            self::ROUNDING     => __('accounts::enums/display-type.rounding'),
            self::PAYMENT_TERM => __('accounts::enums/display-type.payment_term'),
            self::LINE_SECTION => __('accounts::enums/display-type.line_section'),
            self::LINE_NOTE    => __('accounts::enums/display-type.line_note'),
            self::EPD          => __('accounts::enums/display-type.epd'),
        };
    }

    public static function options(): array
    {
        return [
            self::PRODUCT->value      => __('accounts::enums/display-type.product'),
            self::COGS->value         => __('accounts::enums/display-type.cogs'),
            self::TAX->value          => __('accounts::enums/display-type.tax'),
            self::DISCOUNT->value     => __('accounts::enums/display-type.discount'),
            self::ROUNDING->value     => __('accounts::enums/display-type.rounding'),
            self::PAYMENT_TERM->value => __('accounts::enums/display-type.payment_term'),
            self::LINE_SECTION->value => __('accounts::enums/display-type.line_section'),
            self::LINE_NOTE->value    => __('accounts::enums/display-type.line_note'),
            self::EPD->value          => __('accounts::enums/display-type.epd'),
        ];
    }
}
