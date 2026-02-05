<?php

namespace Webkul\Contact\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Contact\Models\Title;
use Webkul\Security\Models\User;

class TitlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_contact_title');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_contact_title');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Title $title): bool
    {
        return $user->can('update_contact_title');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Title $title): bool
    {
        return $user->can('delete_contact_title');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_contact_title');
    }
}
