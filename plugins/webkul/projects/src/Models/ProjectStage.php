<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Project\Database\Factories\ProjectStageFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class ProjectStage extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'projects_project_stages';

    protected $fillable = [
        'name',
        'is_active',
        'is_collapsed',
        'sort',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_collapsed' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'stage_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($projectStage) {
            $projectStage->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): ProjectStageFactory
    {
        return ProjectStageFactory::new();
    }
}
