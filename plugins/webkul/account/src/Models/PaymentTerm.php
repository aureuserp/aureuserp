<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PaymentTerm extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'accounts_payment_terms';

    protected $fillable = [
        'company_id',
        'sort',
        'discount_days',
        'creator_id',
        'early_pay_discount',
        'name',
        'note',
        'display_on_invoice',
        'early_discount',
        'discount_percentage',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function dueTerm()
    {
        return $this->hasOne(PaymentDueTerm::class, 'payment_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($paymentTerm) {
            $paymentTerm->dueTerm()->create([
                'value'           => DueTermValue::PERCENT->value,
                'value_amount'    => 100,
                'delay_type'      => DelayType::DAYS_AFTER->value,
                'days_next_month' => 10,
                'nb_days'         => 0,
                'payment_id'      => $paymentTerm->id,
            ]);
        });
    }
}
