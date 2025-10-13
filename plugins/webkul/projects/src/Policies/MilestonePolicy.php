<?php

namespace Webkul\Project\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Project\Models\Milestone;
use Webkul\Security\Models\User;

class MilestonePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_milestone');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Milestone $milestone): bool
    {
        return $user->can('view_milestone');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_milestone');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Milestone $milestone): bool
    {
        return $user->can('update_milestone');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Milestone $milestone): bool
    {
        return $user->can('delete_milestone');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_milestone');
    }
}
