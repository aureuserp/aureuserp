<?php

namespace Webkul\Recruitment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Recruitment\Models\UTMSource;
use Webkul\Security\Models\User;

class UTMSourcePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_u::t::m::source');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UTMSource $uTMSource): bool
    {
        return $user->can('view_u::t::m::source');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_u::t::m::source');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UTMSource $uTMSource): bool
    {
        return $user->can('update_u::t::m::source');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UTMSource $uTMSource): bool
    {
        return $user->can('delete_u::t::m::source');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_u::t::m::source');
    }
}
