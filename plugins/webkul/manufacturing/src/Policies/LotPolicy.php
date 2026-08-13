<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\Lot;
use Webkul\Security\Models\User;

class LotPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_lot');
    }

    public function view(User $user, Lot $lot): bool
    {
        return $user->can('view_manufacturing_lot');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_lot');
    }

    public function update(User $user, Lot $lot): bool
    {
        return $user->can('update_manufacturing_lot');
    }

    public function delete(User $user, Lot $lot): bool
    {
        return $user->can('delete_manufacturing_lot');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_lot');
    }
}
