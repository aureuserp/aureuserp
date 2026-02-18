<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\PaymentType;
use Webkul\Security\Models\User;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'accounts_payment_methods';

    protected $fillable = [
        'code',
        'payment_type',
        'name',
        'creator_id',
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function accountMovePayment()
    {
        return $this->hasMany(Move::class, 'payment_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paymentMethod) {
            $paymentMethod->creator_id ??= Auth::id();
        });
    }
}
