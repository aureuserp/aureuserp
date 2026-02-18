<?php

namespace Webkul\Partner\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Database\Factories\TagFactory;
use Webkul\Security\Models\User;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'partners_tags';

    protected $fillable = [
        'name',
        'color',
        'creator_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            $tag->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
