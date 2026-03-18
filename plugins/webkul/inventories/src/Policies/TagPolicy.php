<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Tag;
use Webkul\Security\Models\User;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_inventory_tag');
    }

    public function view(User $user, Tag $tag): bool
    {
        return $user->can('view_inventory_tag');
    }

    public function create(User $user): bool
    {
        return $user->can('create_inventory_tag');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->can('update_inventory_tag');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->can('delete_inventory_tag');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_inventory_tag');
    }

    public function forceDelete(User $user, Tag $tag): bool
    {
        return $user->can('force_delete_inventory_tag');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_inventory_tag');
    }

    public function restore(User $user, Tag $tag): bool
    {
        return $user->can('restore_inventory_tag');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_inventory_tag');
    }
}
