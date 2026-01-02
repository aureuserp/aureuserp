<?php

namespace Webkul\Account\Settings;

use Spatie\LaravelSettings\Settings;
use Webkul\Account\Enums\TaxIncludeOverride;

class TaxesSettings extends Settings
{
    public int $account_sale_tax_id;

    public int $account_purchase_tax_id;

    public string $tax_calculation_rounding_method;

    public int $account_fiscal_country_id;

    public TaxIncludeOverride $account_price_include;

    public static function group(): string
    {
        return 'accounts_taxes';
    }
}
