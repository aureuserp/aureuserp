<?php

namespace Webkul\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Blog\Database\Factories\CategoryFactory;
use Webkul\Security\Models\User;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blogs_categories';

    protected $fillable = [
        'name',
        'sub_title',
        'slug',
        'image',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'creator_id',
    ];

    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return null;
        }

        return Storage::url($this->image);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
