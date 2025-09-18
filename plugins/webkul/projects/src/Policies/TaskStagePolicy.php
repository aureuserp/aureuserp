<?php

namespace Webkul\Project\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\User;

class TaskStagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_task::stage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskStage $taskStage): bool
    {
        return $user->can('view_task::stage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_task::stage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskStage $taskStage): bool
    {
        return $user->can('update_task::stage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskStage $taskStage): bool
    {
        return $user->can('delete_task::stage');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_task::stage');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TaskStage $taskStage): bool
    {
        return $user->can('force_delete_task::stage');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_task::stage');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TaskStage $taskStage): bool
    {
        return $user->can('restore_task::stage');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_task::stage');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_task::stage');
    }
}
