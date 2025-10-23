<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'iso_numeric',
        'decimal_places',
        'full_name',
        'rounding',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get all states for the country.
     *
     * @return HasMany
     */
    public function rates()
    {
        return $this->hasMany(CurrencyRate::class);
    }
}
