<?php

namespace Webkul\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Country;

class Visitor extends Model
{
    public const CONNECTED_MINUTES = 5;

    protected $table = 'website_visitors';

    protected $fillable = [
        'website_id',
        'partner_id',
        'country_id',
        'lang',
        'access_token',
        'timezone',
        'visit_count',
        'last_connection_datetime',
    ];

    protected $casts = [
        'visit_count'              => 'integer',
        'last_connection_datetime' => 'datetime',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class, 'visitor_id');
    }

    public function lastTrack(): HasOne
    {
        return $this->hasOne(Track::class, 'visitor_id')->latestOfMany('visit_datetime');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->partner?->name ?? __('website::filament/admin/resources/visitor.anonymous', ['id' => $this->getKey()]);
    }

    public function isConnected(): bool
    {
        return (bool) $this->last_connection_datetime?->gt(now()->subMinutes(self::CONNECTED_MINUTES));
    }
}
