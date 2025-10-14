<?php

namespace Webkul\Project\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Project\Models\ProjectStage;
use Webkul\Security\Models\User;

class ProjectStagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_project::stage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectStage $projectStage): bool
    {
        return $user->can('view_project::stage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_project::stage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProjectStage $projectStage): bool
    {
        return $user->can('update_project::stage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProjectStage $projectStage): bool
    {
        return $user->can('delete_project::stage');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_project::stage');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ProjectStage $projectStage): bool
    {
        return $user->can('force_delete_project::stage');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_project::stage');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ProjectStage $projectStage): bool
    {
        return $user->can('restore_project::stage');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_project::stage');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_project::stage');
    }
}
