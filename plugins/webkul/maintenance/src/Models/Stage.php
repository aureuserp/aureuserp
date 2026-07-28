<?php

namespace Webkul\Maintenance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Maintenance\Database\Factories\StageFactory;
use Webkul\Security\Models\User;

class Stage extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'maintenance_stages';

    protected $fillable = [
        'sort',
        'name',
        'done',
        'creator_id',
    ];

    protected $casts = [
        'done' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function getModelTitle(): string
    {
        return __('maintenance::models/stage.title');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'stage_id');
    }

    protected static function newFactory(): StageFactory
    {
        return StageFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $stage): void {
            $stage->creator_id ??= Auth::id();
            $stage->done ??= false;
        });
    }
}
