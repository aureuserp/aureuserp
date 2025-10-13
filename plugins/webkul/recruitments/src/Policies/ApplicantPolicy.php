<?php

namespace Webkul\Recruitment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class ApplicantPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_applicant');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Applicant $applicant): bool
    {
        return $user->can('view_applicant');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_applicant');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Applicant $applicant): bool
    {
        if (! $user->can('update_applicant')) {
            return false;
        }

        return $this->hasAccess($user, $applicant, 'recruiter');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Applicant $applicant): bool
    {
        if (! $user->can('delete_applicant')) {
            return false;
        }

        return $this->hasAccess($user, $applicant, 'recruiter');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_applicant');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Applicant $applicant): bool
    {
        if (! $user->can('force_delete_applicant')) {
            return false;
        }

        return $this->hasAccess($user, $applicant, 'recruiter');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_applicant');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Applicant $applicant): bool
    {
        if (! $user->can('restore_applicant')) {
            return false;
        }

        return $this->hasAccess($user, $applicant, 'recruiter');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_applicant');
    }
}
