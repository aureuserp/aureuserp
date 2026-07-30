<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Account\Database\Factories\PartnerFactory;
use Webkul\Partner\Database\Factories\PartnerFactory as BasePartnerFactory;
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

    protected $appends = [
        'property_account_payable_id',
        'property_account_receivable_id',
        'property_account_position_id',
        'property_payment_term_id',
        'property_supplier_payment_term_id',
        'property_inbound_payment_method_line_id',
        'property_outbound_payment_method_line_id',
    ];

    protected array $pendingCompanyProperties = [];

    protected static function booted(): void
    {
        static::saved(function (self $partner): void {
            $partner->flushPendingCompanyProperties();
        });
    }

    public function getPropertyAccountPayableIdAttribute(): mixed
    {
        return $this->resolveCompanyPropertyAttribute('property_account_payable_id');
    }

    public function setPropertyAccountPayableIdAttribute(mixed $value): void
    {
        $this->pendingCompanyProperties['property_account_payable_id'] = $value;
    }

    public function getPropertyAccountReceivableIdAttribute(): mixed
    {
        return $this->resolveCompanyPropertyAttribute('property_account_receivable_id');
    }

    public function setPropertyAccountReceivableIdAttribute(mixed $value): void
    {
        $this->pendingCompanyProperties['property_account_receivable_id'] = $value;
    }

    public function getPropertyAccountPositionIdAttribute(): mixed
    {
        return $this->resolveCompanyPropertyAttribute('property_account_position_id');
    }

    public function setPropertyAccountPositionIdAttribute(mixed $value): void
    {
        $this->pendingCompanyProperties['property_account_position_id'] = $value;
    }

    public function getPropertyPaymentTermIdAttribute(): mixed
    {
        return $this->resolveCompanyPropertyAttribute('property_payment_term_id');
    }

    public function setPropertyPaymentTermIdAttribute(mixed $value): void
    {
        $this->pendingCompanyProperties['property_payment_term_id'] = $value;
    }

    public function getPropertySupplierPaymentTermIdAttribute(): mixed
    {
        return $this->resolveCompanyPropertyAttribute('property_supplier_payment_term_id');
    }

    public function setPropertySupplierPaymentTermIdAttribute(mixed $value): void
    {
        $this->pendingCompanyProperties['property_supplier_payment_term_id'] = $value;
    }

    public function getPropertyInboundPaymentMethodLineIdAttribute(): mixed
    {
        return $this->resolveCompanyPropertyAttribute('property_inbound_payment_method_line_id');
    }

    public function setPropertyInboundPaymentMethodLineIdAttribute(mixed $value): void
    {
        $this->pendingCompanyProperties['property_inbound_payment_method_line_id'] = $value;
    }

    public function getPropertyOutboundPaymentMethodLineIdAttribute(): mixed
    {
        return $this->resolveCompanyPropertyAttribute('property_outbound_payment_method_line_id');
    }

    public function setPropertyOutboundPaymentMethodLineIdAttribute(mixed $value): void
    {
        $this->pendingCompanyProperties['property_outbound_payment_method_line_id'] = $value;
    }

    public function propertyCompanyId(): ?int
    {
        $companyId = current_company_id() ?: $this->company_id;

        return $companyId ? (int) $companyId : null;
    }

    protected function resolveCompanyPropertyAttribute(string $field): mixed
    {
        if (array_key_exists($field, $this->pendingCompanyProperties)) {
            return $this->pendingCompanyProperties[$field];
        }

        return $this->companyPropertiesFor()?->{$field};
    }

    protected function flushPendingCompanyProperties(): void
    {
        if (! $this->pendingCompanyProperties) {
            return;
        }

        $pending = $this->pendingCompanyProperties;

        $this->pendingCompanyProperties = [];

        $companyId = $this->propertyCompanyId();

        if (! $companyId) {
            return;
        }

        $existing = PartnerCompanyProperty::query()
            ->where('partner_id', $this->id)
            ->where('company_id', $companyId)
            ->first();

        if (! $existing && ! array_filter($pending, fn ($value) => filled($value))) {
            return;
        }

        PartnerCompanyProperty::updateOrCreate(
            ['partner_id' => $this->id, 'company_id' => $companyId],
            $pending,
        );

        $this->unsetRelation('companyProperties');
    }

    public function companyProperties(): HasMany
    {
        return $this->hasMany(PartnerCompanyProperty::class, 'partner_id');
    }

    public function companyPropertiesFor(): ?PartnerCompanyProperty
    {
        $companyId = $this->propertyCompanyId();

        if (! $companyId || ! $this->exists) {
            return null;
        }

        return $this->companyProperties->firstWhere('company_id', $companyId);
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
        return $this->belongsTo(FiscalPosition::class, 'property_account_position_id');
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

    protected static function newFactory(): BasePartnerFactory
    {
        return PartnerFactory::new();
    }
}
