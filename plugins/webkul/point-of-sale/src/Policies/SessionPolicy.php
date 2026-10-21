<?php

namespace Webkul\PointOfSale\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\PointOfSale\Models\Session;
use Webkul\Security\Models\User;

class SessionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'view_any');
    }

    public function view(User $user, Session $session): bool
    {
        return $this->check($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'create');
    }

    public function update(User $user, Session $session): bool
    {
        return $this->check($user, 'update');
    }

    public function delete(User $user, Session $session): bool
    {
        return $this->check($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->check($user, 'delete_any');
    }

    protected function check(User $user, string $ability): bool
    {
        return $user->can("{$ability}_point_of_sale_session");
    }
}
