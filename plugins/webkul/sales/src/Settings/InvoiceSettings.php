<?php

namespace Webkul\Sale\Settings;

use Spatie\LaravelSettings\Settings;
use Webkul\Invoice\Enums\InvoicePolicy;

class InvoiceSettings extends Settings
{
    public InvoicePolicy $invoice_policy;

    public static function group(): string
    {
        return 'sales_invoicing';
    }
}
