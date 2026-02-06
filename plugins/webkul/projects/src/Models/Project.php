<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Database\Factories\ProjectFactory;
use Webkul\Security\Models\Scopes\UserPermissionScope;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasPermissionScope;
use Webkul\Support\Models\Company;

class Project extends Model implements Sortable
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, HasPermissionScope, SoftDeletes, SortableTrait;

    protected $table = 'projects_projects';

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function getModelTitle(): string
    {
        return __('projects::models/project.title');
    }

    protected $fillable = [
        'name',
        'tasks_label',
        'description',
        'visibility',
        'color',
        'sort',
        'start_date',
        'end_date',
        'allocated_hours',
        'allow_timesheets',
        'allow_milestones',
        'allow_task_dependencies',
        'is_active',
        'stage_id',
        'partner_id',
        'company_id',
        'user_id',
        'creator_id',
    ];

    protected $casts = [
        'start_date'              => 'date',
        'end_date'                => 'date',
        'is_active'               => 'boolean',
        'allow_timesheets'        => 'boolean',
        'allow_milestones'        => 'boolean',
        'start_date'              => 'date',
        'end_date'                => 'date',
        'is_active'               => 'boolean',
        'allow_timesheets'        => 'boolean',
        'allow_milestones'        => 'boolean',
        'allow_task_dependencies' => 'boolean',
    ];

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                    => __('projects::models/project.log-attributes.name'),
            'tasks_label'             => __('projects::models/project.log-attributes.tasks_label'),
            'description'             => __('projects::models/project.log-attributes.description'),
            'visibility'              => __('projects::models/project.log-attributes.visibility'),
            'color'                   => __('projects::models/project.log-attributes.color'),
            'sort'                    => __('projects::models/project.log-attributes.sort'),
            'start_date'              => __('projects::models/project.log-attributes.start_date'),
            'end_date'                => __('projects::models/project.log-attributes.end_date'),
            'allocated_hours'         => __('projects::models/project.log-attributes.allocated_hours'),
            'allow_timesheets'        => __('projects::models/project.log-attributes.allow_timesheets'),
            'allow_milestones'        => __('projects::models/project.log-attributes.allow_milestones'),
            'allow_task_dependencies' => __('projects::models/project.log-attributes.allow_task_dependencies'),
            'is_active'               => __('projects::models/project.log-attributes.is_active'),
            'stage.name'              => __('projects::models/project.log-attributes.stage'),
            'partner.name'            => __('projects::models/project.log-attributes.partner'),
            'company.name'            => __('projects::models/project.log-attributes.company'),
            'user.name'               => __('projects::models/project.log-attributes.user'),
            'creator.name'            => __('projects::models/project.log-attributes.creator'),
        ];
    }

    protected function plannedDate(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['start_date'].' - '.$attributes['end_date'],
        );
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProjectStage::class);
    }

    public function taskStages(): HasMany
    {
        return $this->hasMany(TaskStage::class);
    }

    public function favoriteUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'projects_user_project_favorites', 'project_id', 'user_id');
    }

    public function getIsFavoriteByUserAttribute(): bool
    {
        if ($this->relationLoaded('favoriteUsers')) {
            return $this->favoriteUsers->contains('id', Auth::id());
        }

        return $this->favoriteUsers()->where('user_id', Auth::id())->exists();
    }

    public function getRemainingHoursAttribute(): float
    {
        return $this->allocated_hours - $this->tasks->sum('remaining_hours');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'projects_project_tag', 'project_id', 'tag_id');
    }

    protected static function booted()
    {
        static::addGlobalScope(new UserPermissionScope('user'));
    }

    protected static function newFactory(): ProjectFactory
    {
        return ProjectFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            $project->creator_id ??= Auth::id();
        });
    }
}
