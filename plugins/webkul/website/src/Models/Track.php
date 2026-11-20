<?php

namespace Webkul\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Track extends Model
{
    public $timestamps = false;

    protected $table = 'website_tracks';

    protected $fillable = [
        'visitor_id',
        'page_id',
        'url',
        'visit_datetime',
    ];

    protected $casts = [
        'visit_datetime' => 'datetime',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id')->withTrashed();
    }
}
