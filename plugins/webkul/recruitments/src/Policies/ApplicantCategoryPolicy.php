<?php

namespace Webkul\Recruitment\Policies;

use Webkul\Security\Models\User;
use Webkul\Recruitment\Models\ApplicantCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicantCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_applicant::category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApplicantCategory $applicantCategory): bool
    {
        return $user->can('view_applicant::category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_applicant::category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApplicantCategory $applicantCategory): bool
    {
        return $user->can('update_applicant::category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApplicantCategory $applicantCategory): bool
    {
        return $user->can('delete_applicant::category');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_applicant::category');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ApplicantCategory $applicantCategory): bool
    {
        return $user->can('force_delete_applicant::category');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_applicant::category');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ApplicantCategory $applicantCategory): bool
    {
        return $user->can('restore_applicant::category');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_applicant::category');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ApplicantCategory $applicantCategory): bool
    {
        return $user->can('replicate_applicant::category');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_applicant::category');
    }
}
