<?php

namespace Webkul\Recruitment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Recruitment\Models\ApplicantCategory;
use Webkul\Security\Models\User;

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
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_applicant::category');
    }
}
