<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'currency_id',
        'phone_code',
        'code',
        'name',
        'state_required',
        'zip_required',
    ];

    protected $casts = [
        'state_required' => 'boolean',
        'zip_required'   => 'boolean',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id');
    }
}
