<?php

namespace Webkul\Recruitment\Policies;

use Webkul\Security\Models\User;
use Webkul\Recruitment\Models\Candidate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Traits\HasScopedPermissions;

class CandidatePolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_candidate');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Candidate $candidate): bool
    {
        return $user->can('view_candidate');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_candidate');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Candidate $candidate): bool
    {
        if (! $user->can('update_candidate')) {
            return false;
        }

        return $this->hasAccess($user, $candidate, 'manager');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Candidate $candidate): bool
    {
        if (! $user->can('delete_candidate')) {
            return false;
        }

        return $this->hasAccess($user, $candidate, 'manager');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_candidate');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Candidate $candidate): bool
    {
        if (! $user->can('force_delete_candidate')) {
            return false;
        }

        return $this->hasAccess($user, $candidate, 'manager');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_candidate');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Candidate $candidate): bool
    {
        if (! $user->can('restore_candidate')) {
            return false;
        }

        return $this->hasAccess($user, $candidate, 'manager');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_candidate');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Candidate $candidate): bool
    {
        if (! $user->can('replicate_candidate')) {
            return false;
        }

        return $this->hasAccess($user, $candidate, 'manager');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_candidate');
    }
}
