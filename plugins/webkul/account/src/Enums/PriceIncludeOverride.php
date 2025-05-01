<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum PriceIncludeOverride: string implements HasLabel
{
    case TAX_INCLUDED = 'tax_included';

    case TAX_EXCLUDED = 'tax_excluded';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TAX_INCLUDED => __('accounts::enums/price-include-override.included'),
            self::TAX_EXCLUDED => __('accounts::enums/price-include-override.excluded'),
        };
    }

    public static function options(): array
    {
        return [
            self::TAX_INCLUDED->value => __('accounts::enums/price-include-override.included'),
            self::TAX_EXCLUDED->value => __('accounts::enums/price-include-override.excluded'),
        ];
    }
}
