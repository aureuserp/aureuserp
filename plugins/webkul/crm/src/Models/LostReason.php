<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Database\Factories\LostReasonFactory;
use Webkul\Security\Models\User;

class LostReason extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_lost_reasons';

    protected $fillable = [
        'name',
        'creator_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $lostReason): void {
            $lostReason->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): LostReasonFactory
    {
        return LostReasonFactory::new();
    }
}
