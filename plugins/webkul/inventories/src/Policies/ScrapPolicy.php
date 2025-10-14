<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Scrap;
use Webkul\Security\Models\User;

class ScrapPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_scrap');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Scrap $scrap): bool
    {
        return $user->can('view_scrap');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_scrap');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Scrap $scrap): bool
    {
        return $user->can('update_scrap');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Scrap $scrap): bool
    {
        return $user->can('delete_scrap');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_scrap');
    }
}
