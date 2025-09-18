<?php

namespace Webkul\Purchase\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Purchase\Models\Quotation;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class QuotationPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_quotation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quotation $quotation): bool
    {
        return $user->can('view_quotation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_quotation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        if (! $user->can('update_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $quotation);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quotation $quotation): bool
    {
        if (! $user->can('delete_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $quotation);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_quotation');
    }
}
