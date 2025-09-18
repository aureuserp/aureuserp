<?php

namespace Webkul\Sale\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Sale\Models\OrderTemplate;
use Webkul\Security\Models\User;

class OrderTemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_quotation::template');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderTemplate $orderTemplate): bool
    {
        return $user->can('view_quotation::template');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_quotation::template');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderTemplate $orderTemplate): bool
    {
        return $user->can('update_quotation::template');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderTemplate $orderTemplate): bool
    {
        return $user->can('delete_quotation::template');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_quotation::template');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, OrderTemplate $orderTemplate): bool
    {
        return $user->can('force_delete_quotation::template');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_quotation::template');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, OrderTemplate $orderTemplate): bool
    {
        return $user->can('restore_quotation::template');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_quotation::template');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_quotation::template');
    }
}
