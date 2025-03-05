<?php

namespace Webkul\Sale\Settings;

use Spatie\LaravelSettings\Settings;

class InvoiceSettings extends Settings
{
    public string $invoice_policy;

    public static function group(): string
    {
        return 'sales_invoicing';
    }
}
