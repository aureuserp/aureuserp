<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Database\Factories\TaskFactory;
use Webkul\Project\Enums\TaskState;
use Webkul\Security\Models\Scopes\UserPermissionScope;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasPermissionScope;
use Webkul\Support\Models\Company;

class Task extends Model implements Sortable
{
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, HasPermissionScope, SoftDeletes, SortableTrait;

    protected $table = 'projects_tasks';

    public function getModelTitle(): string
    {
        return __('projects::models/task.title');
    }

    protected $fillable = [
        'title',
        'description',
        'color',
        'priority',
        'state',
        'sort',
        'is_active',
        'is_recurring',
        'deadline',
        'working_hours_open',
        'working_hours_close',
        'allocated_hours',
        'remaining_hours',
        'effective_hours',
        'total_hours_spent',
        'subtask_effective_hours',
        'overtime',
        'progress',
        'stage_id',
        'project_id',
        'partner_id',
        'parent_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'deadline'            => 'datetime',
        'priority'            => 'boolean',
        'is_active'           => 'boolean',
        'is_recurring'        => 'boolean',
        'working_hours_open'  => 'float',
        'working_hours_close' => 'float',
        'allocated_hours'     => 'float',
        'remaining_hours'     => 'float',
        'effective_hours'     => 'float',
        'total_hours_spent'   => 'float',
        'overtime'            => 'float',
        'state'               => TaskState::class,
    ];

    protected function getLogAttributeLabels(): array
    {
        return [
            'title'             => __('projects::models/task.log-attributes.title'),
            'description'       => __('projects::models/task.log-attributes.description'),
            'color'             => __('projects::models/task.log-attributes.color'),
            'priority'          => __('projects::models/task.log-attributes.priority'),
            'state'             => __('projects::models/task.log-attributes.state'),
            'sort'              => __('projects::models/task.log-attributes.sort'),
            'is_active'         => __('projects::models/task.log-attributes.is_active'),
            'is_recurring'      => __('projects::models/task.log-attributes.is_recurring'),
            'deadline'          => __('projects::models/task.log-attributes.deadline'),
            'allocated_hours'   => __('projects::models/task.log-attributes.allocated_hours'),
            'stage.name'        => __('projects::models/task.log-attributes.stage'),
            'project.name'      => __('projects::models/task.log-attributes.project'),
            'partner.name'      => __('projects::models/task.log-attributes.partner'),
            'parent.title'      => __('projects::models/task.log-attributes.parent'),
            'company.name'      => __('projects::models/task.log-attributes.company'),
            'creator.name'      => __('projects::models/task.log-attributes.creator'),
        ];
    }

    public string $recordTitleAttribute = 'title';

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setPivotTable('projects_task_users');

        $this->setPivotForeignKey('task_id');

        $this->setPivotRelatedKey('user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(TaskStage::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'projects_task_users');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'projects_task_tag', 'task_id', 'tag_id');
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new UserPermissionScope('users'));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $authUser = Auth::user();

            $task->creator_id ??= $authUser->id;

            $task->company_id ??= $authUser?->default_company_id;
        });

        static::updated(function ($task) {
            $task->timesheets()->update([
                'project_id' => $task->project_id,
                'partner_id' => $task->partner_id ?? $task->project?->partner_id,
                'company_id' => $task->company_id ?? $task->project?->company_id,
            ]);
        });
    }

    protected static function newFactory(): TaskFactory
    {
        return TaskFactory::new();
    }
}
