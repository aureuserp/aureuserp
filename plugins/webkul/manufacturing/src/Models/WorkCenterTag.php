<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class WorkCenterTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'manufacturing_work_center_tags';

    protected $fillable = [
        'name',
        'color',
        'sort',
        'creator_id',
        'deleted_at',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workCenters(): BelongsToMany
    {
        return $this->belongsToMany(WorkCenter::class, 'manufacturing_work_center_tag', 'tag_id', 'work_center_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $tag): void {
            $tag->creator_id ??= Auth::id();
        });
    }
}
