<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Support\Database\Factories\CountryFactory;

class Country extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency_id',
        'phone_code',
        'code',
        'name',
        'state_required',
        'zip_required',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'state_required' => 'boolean',
        'zip_required'   => 'boolean',
    ];

    /**
     * Get the currency associated with the country.
     *
     * @return BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Get all states for the country.
     *
     * @return HasMany
     */
    public function states()
    {
        return $this->hasMany(State::class, 'country_id');
    }

    protected static function newFactory()
    {
        return CountryFactory::new();
    }
}
