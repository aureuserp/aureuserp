<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Webkul\Account\Database\Factories\CashRoundingFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;

class CashRounding extends Model
{
    use HasFactory;

    protected $table = 'accounts_cash_roundings';

    protected $fillable = [
        'creator_id',
        'strategy',
        'rounding_method',
        'name',
        'rounding',
        'profit_account_id',
        'loss_account_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function profitAccount()
    {
        return $this->belongsTo(Account::class, 'profit_account_id');
    }

    public function lossAccount()
    {
        return $this->belongsTo(Account::class, 'loss_account_id');
    }

    public function computeDifference(Currency $currency, float $amount = 0): float
    {
        $amount = $currency->round($amount);

        $difference = $this->round($amount) - $amount;

        return $currency->round($difference);
    }

    public function round(float $amount): float
    {
        return float_round($amount, precisionRounding: $this->rounding, roundingMethod: $this->rounding_method);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cashRounding) {
            $cashRounding->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory()
    {
        return CashRoundingFactory::new();
    }
}
