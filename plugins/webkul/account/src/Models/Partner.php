<?php

namespace Webkul\Account\Models;

use Webkul\Partner\Models\Partner as BasePartner;

class Partner extends BasePartner
{
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'message_bounce',
            'supplier_rank',
            'customer_rank',
            'invoice_warning',
            'autopost_bills',
            'credit_limit',
            'ignore_abnormal_invoice_date',
            'ignore_abnormal_invoice_amount',
            'invoice_sending_method',
            'invoice_edi_format_store',
            'trust',
            'invoice_warn_msg',
            'debit_limit',
            'peppol_endpoint',
            'peppol_eas',
            'sale_warn',
            'comment',
            'sale_warn_msg',
            'property_account_payable_id',
            'property_account_receivable_id',
            'property_account_position_id',
            'property_payment_term_id',
            'property_supplier_payment_term_id',
            'property_outbound_payment_method_line_id',
            'property_inbound_payment_method_line_id',
        ]);

        parent::__construct($attributes);
    }

    public function propertyAccountPayable()
    {
        return $this->belongsTo(Account::class, 'property_account_payable_id');
    }

    public function propertyAccountReceivable()
    {
        return $this->belongsTo(Account::class, 'property_account_receivable_id');
    }

    public function propertyAccountPosition()
    {
        return $this->belongsTo(Account::class, 'property_account_position_id');
    }

    public function propertyPaymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class, 'property_payment_term_id');
    }

    public function propertySupplierPaymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class, 'property_supplier_payment_term_id');
    }

    public function propertyOutboundPaymentMethodLine()
    {
        return $this->belongsTo(PaymentMethodLine::class, 'property_outbound_payment_method_line_id');
    }

    public function propertyInboundPaymentMethodLine()
    {
        return $this->belongsTo(PaymentMethodLine::class, 'property_inbound_payment_method_line_id');
    }
}
