<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Webkul\Account\Database\Factories\IncotermFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class Incoterm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounts_incoterms';

    protected $fillable = [
        'code',
        'name',
        'creator_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($incoterm) {
            $incoterm->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): IncotermFactory
    {
        return IncotermFactory::new();
    }
}
