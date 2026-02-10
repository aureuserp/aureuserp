<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class AdvancedPaymentInvoice extends Model
{
    protected $table = 'sales_advance_payment_invoices';

    protected $fillable = [
        'currency_id',
        'company_id',
        'creator_id',
        'advance_payment_method',
        'fixed_amount',
        'deduct_down_payments',
        'consolidated_billing',
        'amount',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'sales_advance_payment_invoice_order_sales', 'advance_payment_invoice_id', 'order_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($advancedPaymentInvoice) {
            $advancedPaymentInvoice->creator_id ??= Auth::id();
        });
    }
}
