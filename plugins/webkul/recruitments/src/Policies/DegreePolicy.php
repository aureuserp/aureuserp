<?php

namespace Webkul\Recruitment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Recruitment\Models\Degree;
use Webkul\Security\Models\User;

class DegreePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_degree');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Degree $degree): bool
    {
        return $user->can('view_degree');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_degree');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Degree $degree): bool
    {
        return $user->can('update_degree');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Degree $degree): bool
    {
        return $user->can('delete_degree');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_degree');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_degree');
    }
}
