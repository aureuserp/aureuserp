<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Database\Factories\TagFactory;
use Webkul\Security\Models\User;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'crm_tags';

    protected $fillable = [
        'name',
        'color',
        'creator_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $tag): void {
            $tag->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
