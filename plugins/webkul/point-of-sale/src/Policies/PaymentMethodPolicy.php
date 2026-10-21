<?php

namespace Webkul\PointOfSale\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\Security\Models\User;

class PaymentMethodPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'view_any');
    }

    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->check($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'create');
    }

    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->check($user, 'update');
    }

    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->check($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->check($user, 'delete_any');
    }

    public function forceDelete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->check($user, 'force_delete');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->check($user, 'force_delete_any');
    }

    public function restore(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->check($user, 'restore');
    }

    public function restoreAny(User $user): bool
    {
        return $this->check($user, 'restore_any');
    }

    protected function check(User $user, string $ability): bool
    {
        return $user->can("{$ability}_point_of_sale_payment::method");
    }
}
