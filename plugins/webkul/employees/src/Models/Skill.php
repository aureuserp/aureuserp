<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Employee\Database\Factories\SkillFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;

class Skill extends Model implements Sortable
{
    use HasCustomFields, HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'employees_skills';

    protected $fillable = [
        'sort',
        'name',
        'skill_type_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function skillType(): BelongsTo
    {
        return $this->belongsTo(SkillType::class, 'skill_type_id');
    }

    public function skillLevels()
    {
        return $this->hasMany(SkillLevel::class);
    }

    public function employeeSkills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class, 'skill_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($skill) {
            $skill->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): SkillFactory
    {
        return SkillFactory::new();
    }
}
