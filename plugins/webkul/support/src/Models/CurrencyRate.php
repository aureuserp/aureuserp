<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Webkul\Support\Database\Factories\CurrencyRateFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class CurrencyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'currency_id',
        'creator_id',
        'company_id',
        'created_at',
    ];

    protected $casts = [
        'name' => 'date',
        'rate' => 'decimal:6',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getInverseRateAttribute()
    {
        if ($this->rate == 0) {
            return null;
        }

        return 1 / $this->rate;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($currencyRate) {
            $currencyRate->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): CurrencyRateFactory
    {
        return CurrencyRateFactory::new();
    }
}
