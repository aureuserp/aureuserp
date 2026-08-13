<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\ChecksCompanyConsistency;

class PartnerCompanyProperty extends Model
{
    use BelongsToCompany;
    use ChecksCompanyConsistency;

    protected $table = 'partners_partner_company_properties';

    protected $fillable = [
        'partner_id',
        'company_id',
        'property_account_payable_id',
        'property_account_receivable_id',
        'property_account_position_id',
        'property_payment_term_id',
        'property_supplier_payment_term_id',
        'property_inbound_payment_method_line_id',
        'property_outbound_payment_method_line_id',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function propertyAccountPayable(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'property_account_payable_id');
    }

    public function propertyAccountReceivable(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'property_account_receivable_id');
    }

    public function propertyAccountPosition(): BelongsTo
    {
        return $this->belongsTo(FiscalPosition::class, 'property_account_position_id');
    }

    public function propertyPaymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class, 'property_payment_term_id');
    }

    public function propertySupplierPaymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class, 'property_supplier_payment_term_id');
    }

    public function propertyInboundPaymentMethodLine(): BelongsTo
    {
        return $this->belongsTo(PaymentMethodLine::class, 'property_inbound_payment_method_line_id');
    }

    public function propertyOutboundPaymentMethodLine(): BelongsTo
    {
        return $this->belongsTo(PaymentMethodLine::class, 'property_outbound_payment_method_line_id');
    }

    public function companyConsistentFields(): array
    {
        return [
            'property_account_payable_id'    => Account::class,
            'property_account_receivable_id' => Account::class,
            'property_account_position_id'   => FiscalPosition::class,
            'property_payment_term_id'       => PaymentTerm::class,
        ];
    }
}
