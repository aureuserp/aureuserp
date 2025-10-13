<?php

namespace Webkul\Project\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class TaskPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_task');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->can('view_task');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_task');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if (! $user->can('update_task')) {
            return false;
        }

        return $this->hasAccess($user, $task, 'users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if (! $user->can('delete_task')) {
            return false;
        }

        return $this->hasAccess($user, $task, 'users');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_task');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        if (! $user->can('force_delete_task')) {
            return false;
        }

        return $this->hasAccess($user, $task, 'users');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_task');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Task $task): bool
    {
        if (! $user->can('restore_task')) {
            return false;
        }

        return $this->hasAccess($user, $task, 'users');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_task');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_task');
    }
}
