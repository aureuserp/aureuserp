<?php

namespace Webkul\Partner\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Database\Factories\IndustryFactory;
use Webkul\Security\Models\User;

class Industry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'partners_industries';

    protected $fillable = [
        'name',
        'description',
        'creator_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($industry) {
            $industry->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): IndustryFactory
    {
        return IndustryFactory::new();
    }
}
