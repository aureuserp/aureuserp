<?php

namespace App\Policies;

use Webkul\Security\Models\User;
use Webkul\Account\Models\CashRounding;
use Illuminate\Auth\Access\HandlesAuthorization;

class CashRoundingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_cash::rounding');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CashRounding $cashRounding): bool
    {
        return $user->can('view_cash::rounding');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_cash::rounding');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CashRounding $cashRounding): bool
    {
        return $user->can('update_cash::rounding');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CashRounding $cashRounding): bool
    {
        return $user->can('delete_cash::rounding');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_cash::rounding');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, CashRounding $cashRounding): bool
    {
        return $user->can('force_delete_cash::rounding');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_cash::rounding');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, CashRounding $cashRounding): bool
    {
        return $user->can('restore_cash::rounding');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_cash::rounding');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, CashRounding $cashRounding): bool
    {
        return $user->can('replicate_cash::rounding');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_cash::rounding');
    }
}
