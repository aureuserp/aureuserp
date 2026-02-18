<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Product\Database\Factories\AttributeFactory;
use Webkul\Product\Enums\AttributeType;
use Webkul\Security\Models\User;

class Attribute extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'products_attributes';

    protected $fillable = [
        'name',
        'type',
        'sort',
        'creator_id',
    ];

    protected $casts = [
        'type' => AttributeType::class,
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): AttributeFactory
    {
        return AttributeFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            $attribute->creator_id ??= Auth::id();
        });
    }
}
