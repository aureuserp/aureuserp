<?php

namespace Webkul\Account\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Account\Models\CreditNote;
use Webkul\Security\Models\User;

class CreditNotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_account_credit::note');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CreditNote $creditNote): bool
    {
        return $user->can('view_account_credit::note');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_account_credit::note');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CreditNote $creditNote): bool
    {
        return $user->can('update_account_credit::note');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CreditNote $creditNote): bool
    {
        return $user->can('delete_account_credit::note');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_account_credit::note');
    }
}
