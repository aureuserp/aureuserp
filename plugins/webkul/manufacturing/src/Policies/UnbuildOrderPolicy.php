<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\UnbuildOrder;
use Webkul\Security\Models\User;

class UnbuildOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_unbuild::order');
    }

    public function view(User $user, UnbuildOrder $unbuildOrder): bool
    {
        return $user->can('view_manufacturing_unbuild::order');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_unbuild::order');
    }

    public function update(User $user, UnbuildOrder $unbuildOrder): bool
    {
        return $user->can('update_manufacturing_unbuild::order');
    }

    public function delete(User $user, UnbuildOrder $unbuildOrder): bool
    {
        return $user->can('delete_manufacturing_unbuild::order');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_unbuild::order');
    }
}
