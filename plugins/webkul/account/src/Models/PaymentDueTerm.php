<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
