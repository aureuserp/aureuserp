<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'accounts_account_tags';

    protected $fillable = [
        'color',
        'country_id',
        'creator_id',
        'applicability',
        'name',
        'tax_negate',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            $tag->creator_id ??= Auth::id();
        });
    }
}
