<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\PackageType;
use Webkul\Security\Models\User;

class PackageTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_package::type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PackageType $packageType): bool
    {
        return $user->can('view_package::type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_package::type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PackageType $packageType): bool
    {
        return $user->can('update_package::type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PackageType $packageType): bool
    {
        return $user->can('delete_package::type');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_package::type');
    }
}
