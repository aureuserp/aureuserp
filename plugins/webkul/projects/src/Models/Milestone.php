<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Database\Factories\MilestoneFactory;
use Webkul\Security\Models\User;

class Milestone extends Model
{
    use HasFactory;

    protected $table = 'projects_milestones';

    protected $fillable = [
        'name',
        'deadline',
        'is_completed',
        'completed_at',
        'project_id',
        'creator_id',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'deadline'     => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($milestone) {
            $milestone->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): MilestoneFactory
    {
        return MilestoneFactory::new();
    }
}
