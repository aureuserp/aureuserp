<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Crm\Database\Factories\StageFactory;
use Webkul\Security\Models\User;

class Stage extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'crm_stages';

    protected $fillable = [
        'sort',
        'name',
        'is_won',
        'requirements',
        'creator_id',
    ];

    protected $casts = [
        'is_won' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'stage_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $stage): void {
            $stage->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): StageFactory
    {
        return StageFactory::new();
    }
}
