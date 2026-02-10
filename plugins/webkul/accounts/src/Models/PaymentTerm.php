<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Account\Database\Factories\PaymentTermFactory;
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function dueTerms()
    {
        return $this->hasMany(PaymentDueTerm::class, 'payment_id');
    }

    public function moves()
    {
        return $this->hasMany(Move::class, 'invoice_payment_term_id');
    }

    public function getEarlyDiscountAttribute()
    {
        return false;
    }

    public function computeTerms(
        $dateRef,
        $currency,
        $company,
        $taxAmount,
        $taxAmountCurrency,
        $sign,
        $untaxedAmount,
        $untaxedAmountCurrency,
        $cashRounding = null
    ) {
        $companyCurrency = $company->currency;

        $totalAmount = $taxAmount + $untaxedAmount;

        $totalAmountCurrency = $taxAmountCurrency + $untaxedAmountCurrency;

        $rate = $totalAmount ? abs($totalAmountCurrency / $totalAmount) : 0.0;

        $paymentTerm = [
            'total_amount'        => $totalAmount,
            'discount_percentage' => $this->early_discount ? $this->discount_percentage : 0.0,
            'discount_date'       => $this->early_discount ? $dateRef->copy()->addDays($this->discount_days ?? 0) : null,
            'discount_balance'    => 0,
            'lines'               => [],
        ];

        if ($this->early_discount) {
            $discountPercentage = $this->discount_percentage / 100.0;

            if (in_array($this->early_pay_discount_computation, ['excluded', 'mixed'])) {
                $paymentTerm['discount_balance'] = $companyCurrency->round($totalAmount - $untaxedAmount * $discountPercentage);

                $paymentTerm['discount_amount_currency'] = $currency->round($totalAmountCurrency - $untaxedAmountCurrency * $discountPercentage);
            } else {
                $paymentTerm['discount_balance'] = $companyCurrency->round($totalAmount * (1 - $discountPercentage));

                $paymentTerm['discount_amount_currency'] = $currency->round($totalAmountCurrency * (1 - $discountPercentage));
            }

            if ($cashRounding) {
                $cashRoundingDifferenceCurrency = $cashRounding->computeDifference($currency, $paymentTerm['discount_amount_currency']);

                if (! $currency->isZero($cashRoundingDifferenceCurrency)) {
                    $paymentTerm['discount_amount_currency'] += $cashRoundingDifferenceCurrency;
                    $paymentTerm['discount_balance'] = $rate ? $companyCurrency->round($paymentTerm['discount_amount_currency'] / $rate) : 0.0;
                }
            }
        }

        $residualAmount = $totalAmount;

        $residualAmountCurrency = $totalAmountCurrency;

        foreach ($this->dueTerms as $i => $line) {
            $termVals = [
                'date'           => $line->getDueDate($dateRef),
                'company_amount' => 0,
                'foreign_amount' => 0,
            ];

            $onBalanceLine = $i === count($this->dueTerms) - 1;

            if ($onBalanceLine) {
                $termVals['company_amount'] = $residualAmount;

                $termVals['foreign_amount'] = $residualAmountCurrency;
            } elseif ($line->value === 'fixed') {
                $termVals['company_amount'] = $rate ? $sign * $companyCurrency->round($line->value_amount / $rate) : 0.0;

                $termVals['foreign_amount'] = $sign * $currency->round($line->value_amount);
            } else {
                $lineAmount = $companyCurrency->round($totalAmount * ($line->value_amount / 100.0));

                $lineAmountCurrency = $currency->round($totalAmountCurrency * ($line->value_amount / 100.0));

                $termVals['company_amount'] = $lineAmount;

                $termVals['foreign_amount'] = $lineAmountCurrency;
            }

            if ($cashRounding && ! $onBalanceLine) {
                $cashRoundingDifferenceCurrency = $cashRounding->computeDifference($currency, $termVals['foreign_amount']);

                if (! $currency->isZero($cashRoundingDifferenceCurrency)) {
                    $termVals['foreign_amount'] += $cashRoundingDifferenceCurrency;

                    $termVals['company_amount'] = $rate ? $companyCurrency->round($termVals['foreign_amount'] / $rate) : 0.0;
                }
            }

            $residualAmount -= $termVals['company_amount'];

            $residualAmountCurrency -= $termVals['foreign_amount'];

            $paymentTerm['lines'][] = $termVals;
        }

        return $paymentTerm;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paymentTerm) {
            $paymentTerm->creator_id ??= Auth::id();
        });

        static::created(function ($paymentTerm) {
            $paymentTerm->dueTerms()->create([
                'value'           => DueTermValue::PERCENT->value,
                'value_amount'    => 100,
                'delay_type'      => DelayType::DAYS_AFTER->value,
                'days_next_month' => 10,
                'nb_days'         => 0,
                'payment_id'      => $paymentTerm->id,
            ]);
        });
    }

    protected static function newFactory(): PaymentTermFactory
    {
        return PaymentTermFactory::new();
    }
}
