<?php

namespace Webkul\Chatter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Webkul\Partner\Models\Partner;

class Follower extends Model
{
    protected $table = 'chatter_followers';

    protected $fillable = [
        'followable_id',
        'followable_type',
        'partner_id',
        'followed_at',
    ];

    protected $casts = [
        'followed_at' => 'datetime',
    ];

    public function followable(): MorphTo
    {
        return $this->morphTo();
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id')->withTrashed();
    }
}
