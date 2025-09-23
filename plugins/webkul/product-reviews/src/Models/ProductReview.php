<?php

namespace Webkul\ProductReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Webkul\Security\Models\User;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'product-reviews';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'creator_id',
    ];

    /**
     * Casts.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get image url for the image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return null;
        }

        return Storage::url($this->image);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}