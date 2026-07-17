<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;

class QuickNavigationFavorite extends Model
{
    protected $table = 'quick_navigation_favorites';

    protected $fillable = [
        'user_id',
        'label',
        'url',
        'sort',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
