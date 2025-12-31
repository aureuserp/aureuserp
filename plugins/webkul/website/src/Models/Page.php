<?php

namespace Webkul\Website\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Security\Models\User;
use Webkul\Website\Database\Factories\PageFactory;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'website_pages';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'slug',
        'is_published',
        'published_at',
        'is_header_visible',
        'is_footer_visible',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'creator_id',
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'is_published'      => 'boolean',
        'is_header_visible' => 'boolean',
        'is_footer_visible' => 'boolean',
        'published_at'      => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($page) {
            $page->creator_id = filament()->auth()->id();
        });
    }

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }
}
