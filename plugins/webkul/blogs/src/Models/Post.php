<?php

namespace Webkul\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Webkul\Blog\Database\Factories\PostFactory;
use Webkul\Security\Models\User;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'blogs_posts';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'sub_title',
        'content',
        'slug',
        'image',
        'author_name',
        'is_published',
        'published_at',
        'visits',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'category_id',
        'author_id',
        'creator_id',
        'last_editor_id',
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get image url for the product image.
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

    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));

        $minutes = ceil($wordCount / 200);

        return $minutes.' min read';
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blogs_post_tags', 'post_id', 'tag_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastEditor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->author_id = filament()->auth()->id();

            $post->creator_id = filament()->auth()->id();
        });
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }
}
