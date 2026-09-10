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
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class PaymentTerm extends Model implements Sortable
{
    use BelongsToCompany;
    use HasCustomFields, HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'accounts_payment_terms';

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

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
        $totalAmount = $taxAmount + $untaxedAmount;

        $totalAmountCurrency = $taxAmountCurrency + $untaxedAmountCurrency;

        $rate = $totalAmount ? abs($totalAmountCurrency / $totalAmount) : 0.0;

        $schedule = array_merge([
            'total_amount'        => $totalAmount,
            'discount_percentage' => $this->early_discount ? $this->discount_percentage : 0.0,
            'discount_date'       => $this->early_discount ? $dateRef->copy()->addDays($this->discount_days ?? 0) : null,
            'discount_balance'    => 0,
            'lines'               => [],
        ], $this->earlyDiscount($currency, $company, $untaxedAmount, $untaxedAmountCurrency, $totalAmount, $totalAmountCurrency, $rate, $cashRounding));

        $schedule['lines'] = $this->dueLines(
            $dateRef,
            $currency,
            $company,
            $sign,
            $totalAmount,
            $totalAmountCurrency,
            $rate,
            $cashRounding
        );

        return $schedule;
    }

    protected function earlyDiscount(
        $currency,
        $company,
        $untaxedAmount,
        $untaxedAmountCurrency,
        $totalAmount,
        $totalAmountCurrency,
        float $rate,
        $cashRounding
    ): array {
        if (! $this->early_discount) {
            return [];
        }

        $companyCurrency = $company->currency;

        $discount = $this->discount_percentage / 100.0;

        $excludesTax = in_array($this->early_pay_discount, ['excluded', 'mixed']);

        $discounted = $excludesTax
            ? [
                'discount_balance'         => $companyCurrency->round($totalAmount - $untaxedAmount * $discount),
                'discount_amount_currency' => $currency->round($totalAmountCurrency - $untaxedAmountCurrency * $discount),
            ]
            : [
                'discount_balance'         => $companyCurrency->round($totalAmount * (1 - $discount)),
                'discount_amount_currency' => $currency->round($totalAmountCurrency * (1 - $discount)),
            ];

        if (! $cashRounding) {
            return $discounted;
        }

        $difference = $cashRounding->computeDifference($currency, $discounted['discount_amount_currency']);

        if ($currency->isZero($difference)) {
            return $discounted;
        }

        $discounted['discount_amount_currency'] += $difference;

        $discounted['discount_balance'] = $rate
            ? $companyCurrency->round($discounted['discount_amount_currency'] / $rate)
            : 0.0;

        return $discounted;
    }

    protected function dueLines(
        $dateRef,
        $currency,
        $company,
        $sign,
        $totalAmount,
        $totalAmountCurrency,
        float $rate,
        $cashRounding
    ): array {
        $companyCurrency = $company->currency;

        $residualAmount = $totalAmount;

        $residualAmountCurrency = $totalAmountCurrency;

        $lastIndex = count($this->dueTerms) - 1;

        $lines = [];

        foreach ($this->dueTerms as $index => $dueTerm) {
            $isBalancingLine = $index === $lastIndex;

            $line = [
                'date'           => $dueTerm->getDueDate($dateRef),
                'company_amount' => 0,
                'foreign_amount' => 0,
            ];

            if ($isBalancingLine) {
                $line['company_amount'] = $residualAmount;
                $line['foreign_amount'] = $residualAmountCurrency;
            } elseif ($dueTerm->value === 'fixed') {
                $line['company_amount'] = $rate ? $sign * $companyCurrency->round($dueTerm->value_amount / $rate) : 0.0;
                $line['foreign_amount'] = $sign * $currency->round($dueTerm->value_amount);
            } else {
                $share = $dueTerm->value_amount / 100.0;

                $line['company_amount'] = $companyCurrency->round($totalAmount * $share);
                $line['foreign_amount'] = $currency->round($totalAmountCurrency * $share);
            }

            if ($cashRounding && ! $isBalancingLine) {
                $difference = $cashRounding->computeDifference($currency, $line['foreign_amount']);

                if (! $currency->isZero($difference)) {
                    $line['foreign_amount'] += $difference;

                    $line['company_amount'] = $rate ? $companyCurrency->round($line['foreign_amount'] / $rate) : 0.0;
                }
            }

            $residualAmount -= $line['company_amount'];

            $residualAmountCurrency -= $line['foreign_amount'];

            $lines[] = $line;
        }

        return $lines;
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
