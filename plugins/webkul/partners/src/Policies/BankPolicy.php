<?php

namespace Webkul\Partner\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Partner\Models\Bank;
use Webkul\Security\Models\User;


class BankPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bank');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bank $bank): bool
    {
        return $user->can('view_bank');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_bank');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bank $bank): bool
    {
        return $user->can('update_bank');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bank $bank): bool
    {
        return $user->can('delete_bank');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_bank');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Bank $bank): bool
    {
        return $user->can('force_delete_bank');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_bank');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Bank $bank): bool
    {
        return $user->can('restore_bank');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_bank');
    }
}
