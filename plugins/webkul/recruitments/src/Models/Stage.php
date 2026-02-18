<?php

namespace Webkul\Recruitment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Employee\Models\EmployeeJobPosition;
use Webkul\Security\Models\User;

class Stage extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    protected $table = 'recruitments_stages';

    protected $fillable = [
        'sort',
        'is_default',
        'creator_id',
        'name',
        'legend_blocked',
        'legend_done',
        'legend_normal',
        'requirements',
        'fold',
        'hired_stage',
    ];

    protected $casts = [
        'is_default'  => 'boolean',
        'hired_stage' => 'boolean',
        'fold'        => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function jobs()
    {
        return $this->belongsToMany(EmployeeJobPosition::class, 'recruitments_stages_jobs', 'stage_id', 'job_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stage) {
            $stage->creator_id ??= Auth::id();
        });
    }
}
