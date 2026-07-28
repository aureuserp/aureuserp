<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\Product;
use Webkul\Security\Models\User;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_product');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->can('view_manufacturing_product');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_product');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->can('update_manufacturing_product');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->can('delete_manufacturing_product');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_product');
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->can('force_delete_manufacturing_product');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_manufacturing_product');
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->can('restore_manufacturing_product');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_manufacturing_product');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_manufacturing_product');
    }
}
