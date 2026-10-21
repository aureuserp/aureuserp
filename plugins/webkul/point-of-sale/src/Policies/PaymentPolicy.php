<?php

namespace Webkul\PointOfSale\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\PointOfSale\Models\Payment;
use Webkul\Security\Models\User;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'view_any');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $this->check($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'create');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $this->check($user, 'update');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $this->check($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->check($user, 'delete_any');
    }

    protected function check(User $user, string $ability): bool
    {
        return $user->can("{$ability}_point_of_sale_payment");
    }
}
