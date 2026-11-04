<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Sale\Models\Quotation as Order;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class QuotationPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_quotation');
    }

    public function view(User $user, Order $order): bool
    {
        if (! $user->can('view_crm_quotation')) {
            return false;
        }

        return Order::query()->whereKey($order->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_quotation');
    }

    public function update(User $user, Order $order): bool
    {
        if (! $user->can('update_crm_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $order);
    }

    public function delete(User $user, Order $order): bool
    {
        if (! $user->can('delete_crm_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $order);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_quotation');
    }

    public function forceDelete(User $user, Order $order): bool
    {
        if (! $user->can('force_delete_crm_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $order);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_quotation');
    }

    public function restore(User $user, Order $order): bool
    {
        if (! $user->can('restore_crm_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $order);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_quotation');
    }
}
