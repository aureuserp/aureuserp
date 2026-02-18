<?php

namespace Webkul\Account\Http\Requests;

class RefundRequest extends InvoiceRequest
{
    // Refund uses the same validation rules as Invoice
    // The difference is handled in the controller by setting move_type
}
