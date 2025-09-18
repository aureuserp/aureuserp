<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\EmployeeCategory;
use Webkul\Security\Models\User;

class EmployeeCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employee::category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployeeCategory $employeeCategory): bool
    {
        return $user->can('view_employee::category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_employee::category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmployeeCategory $employeeCategory): bool
    {
        return $user->can('update_employee::category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployeeCategory $employeeCategory): bool
    {
        return $user->can('delete_employee::category');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_employee::category');
    }
}
