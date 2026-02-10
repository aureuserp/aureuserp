<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Employee\Database\Factories\SkillLevelFactory;

class SkillLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employees_skill_levels';

    protected $fillable = [
        'name',
        'skill_type_id',
        'level',
        'default_level',
    ];

    public function skillType(): BelongsTo
    {
        return $this->belongsTo(SkillType::class, 'skill_type_id');
    }

    public function employeeSkills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class, 'skill_level_id');
    }

    protected static function newFactory(): SkillLevelFactory
    {
        return SkillLevelFactory::new();
    }
}
