<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Employee\Database\Factories\EmployeeSkillFactory;
use Webkul\Security\Models\User;

class EmployeeSkill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employees_employee_skills';

    protected $fillable = [
        'employee_id',
        'skill_id',
        'skill_level_id',
        'skill_type_id',
        'creator_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function skillLevel()
    {
        return $this->belongsTo(SkillLevel::class);
    }

    public function skillType()
    {
        return $this->belongsTo(SkillType::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the factory instance for the model.
     */
    protected static function newFactory(): EmployeeSkillFactory
    {
        return EmployeeSkillFactory::new();
    }
}
