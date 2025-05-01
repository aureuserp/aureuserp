<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum TaxIncludeOverride: string implements HasLabel
{
    case DEFAULT = 'default';

    case TAX_INCLUDED = 'tax_included';

    case TAX_EXCLUDED = 'tax_excluded';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DEFAULT      => __('accounts::enums/tax-include-override.default'),
            self::TAX_EXCLUDED => __('accounts::enums/tax-include-override.excluded'),
            self::TAX_INCLUDED => __('accounts::enums/tax-include-override.included'),
        };
    }

    public static function options(): array
    {
        return [
            self::DEFAULT->value      => __('accounts::enums/tax-include-override.default'),
            self::TAX_EXCLUDED->value => __('accounts::enums/tax-include-override.excluded'),
            self::TAX_INCLUDED->value => __('accounts::enums/tax-include-override.included'),
        ];
    }
}
