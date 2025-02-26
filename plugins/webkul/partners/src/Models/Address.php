<?php

namespace Webkul\Partner\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Partner\Database\Factories\AddressFactory;
use Webkul\Partner\Enums\AddressType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class Address extends Model
{
    use HasFactory;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'partners_addresses';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'phone',
        'street1',
        'street2',
        'city',
        'zip',
        'state_id',
        'country_id',
        'creator_id',
        'partner_id',
    ];

    /**
     * Table name.
     *address
     * @var string
     */
    protected $casts = [
        'type' => AddressType::class,
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): AddressFactory
    {
        return AddressFactory::new();
    }
}
