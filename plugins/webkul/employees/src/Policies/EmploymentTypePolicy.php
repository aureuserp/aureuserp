<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\EmploymentType;
use Webkul\Security\Models\User;

class EmploymentTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employment::type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmploymentType $employmentType): bool
    {
        return $user->can('view_employment::type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_employment::type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmploymentType $employmentType): bool
    {
        return $user->can('update_employment::type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmploymentType $employmentType): bool
    {
        return $user->can('delete_employment::type');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_employment::type');
    }
}
