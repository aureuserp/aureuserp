<?php

namespace Webkul\Support\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Bank;

class BankPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_supporbank');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bank $bank): bool
    {
        return $user->can('view_support_acbank');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_support_bank');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bank $bank): bool
    {
        return $user->can('update_support_bank');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bank $bank): bool
    {
        return $user->can('delete_support_bank');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_suppbank');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Bank $bank): bool
    {
        return $user->can('force_delete_subank');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_anbank');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Bank $bank): bool
    {
        return $user->can('restore_supportbank');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_supbank');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_supportbank');
    }
}
