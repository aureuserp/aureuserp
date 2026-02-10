<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\UOMCategoryFactory;

class UOMCategory extends Model
{
    use HasFactory;

    protected $table = 'unit_of_measure_categories';

    protected $fillable = [
        'name',
        'creator_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uoms(): HasMany
    {
        return $this->hasMany(UOM::class, 'category_id');
    }

    protected static function newFactory(): UOMCategoryFactory
    {
        return UOMCategoryFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($uomCategory) {
            $uomCategory->creator_id ??= Auth::id();
        });
    }
}
