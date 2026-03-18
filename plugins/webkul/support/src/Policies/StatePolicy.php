<?php

namespace Webkul\Support\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Support\Models\State;

class StatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_support_state');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, State $state): bool
    {
        return $user->can('view_support_state');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_support_state');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, State $state): bool
    {
        return $user->can('update_support_state');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, State $state): bool
    {
        return $user->can('delete_support_state');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_support_state');
    }
}
