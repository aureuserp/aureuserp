<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\Order;
use Webkul\Security\Models\User;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'view_any');
    }

    public function view(User $user, Order $order): bool
    {
        return $this->check($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'create');
    }

    public function update(User $user, Order $order): bool
    {
        return $this->check($user, 'update');
    }

    public function delete(User $user, Order $order): bool
    {
        return $this->check($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->check($user, 'delete_any');
    }

    protected function check(User $user, string $ability): bool
    {
        return $user->can("{$ability}_manufacturing_order")
            || $user->can("{$ability}_manufacturing_manufacturing::order");
    }
}
