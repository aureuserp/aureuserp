<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;

class CurrencyRate extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'currency_id',
        'creator_id',
        'company_id',
        'created_at',
    ];

    protected $casts = [
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
}
