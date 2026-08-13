<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Security\Models\User;

class OperationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_operation');
    }

    public function view(User $user, Operation $operation): bool
    {
        return $user->can('view_manufacturing_operation');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_operation');
    }

    public function update(User $user, Operation $operation): bool
    {
        return $user->can('update_manufacturing_operation');
    }

    public function delete(User $user, Operation $operation): bool
    {
        return $user->can('delete_manufacturing_operation');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_operation');
    }

    public function forceDelete(User $user, Operation $operation): bool
    {
        return $user->can('force_delete_manufacturing_operation');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_manufacturing_operation');
    }

    public function restore(User $user, Operation $operation): bool
    {
        return $user->can('restore_manufacturing_operation');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_manufacturing_operation');
    }
}
