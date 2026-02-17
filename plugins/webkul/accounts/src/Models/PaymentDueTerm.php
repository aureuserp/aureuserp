<?php

namespace Webkul\Account\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Webkul\Account\Database\Factories\PaymentDueTermFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class PaymentDueTerm extends Model
{
    use HasFactory;

    protected $table = 'accounts_payment_due_terms';

    protected $fillable = [
        'nb_days',
        'payment_id',
        'creator_id',
        'value',
        'delay_type',
        'days_next_month',
        'value_amount',
    ];

    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class, 'payment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getDueDate($dateRef)
    {
        $dueDate = $dateRef ? Carbon::parse($dateRef) : Carbon::today();

        if ($this->delay_type === 'days_after_end_of_month') {
            return $dueDate->copy()->endOfMonth()->addDays($this->nb_days);
        } elseif ($this->delay_type === 'days_after_end_of_next_month') {
            return $dueDate->copy()->addMonthNoOverflow(1)->endOfMonth()->addDays($this->nb_days);
        } elseif ($this->delay_type === 'days_end_of_month_on_the') {
            $daysNextMonth = 1;

            if (is_numeric($this->days_next_month)) {
                $daysNextMonth = (int) $this->days_next_month;
            }

            if (! $daysNextMonth) {
                return $dueDate->copy()->addDays($this->nb_days)->endOfMonth();
            }

            return $dueDate->copy()->addDays($this->nb_days)->addMonthsNoOverflow(1)->day($daysNextMonth);
        }

        return $dueDate->copy()->addDays($this->nb_days);
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paymentDueTerm) {
            $paymentDueTerm->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): PaymentDueTermFactory
    {
        return PaymentDueTermFactory::new();
    }
}
