<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\EmployeeSkill;
use Webkul\Security\Models\User;

class EmployeeSkillPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employee::skill');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployeeSkill $employeeSkill): bool
    {
        return $user->can('view_employee::skill');
    }
}
