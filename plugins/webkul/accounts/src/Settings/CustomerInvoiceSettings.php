<?php

namespace Webkul\Account\Settings;

use Spatie\LaravelSettings\Settings;

class CustomerInvoiceSettings extends Settings
{
    public bool $group_cash_rounding;

    public ?int $incoterm_id;

    public static function group(): string
    {
        return 'accounts_customer_invoice';
    }
}
