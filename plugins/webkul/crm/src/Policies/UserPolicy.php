<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class UserPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_user');
    }

    public function view(User $user, User $record): bool
    {
        if (! $user->can('view_crm_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'creator');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_user');
    }

    public function update(User $user, User $record): bool
    {
        if (! $user->can('update_crm_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'creator');
    }

    public function delete(User $user, User $record): bool
    {
        if (! $user->can('delete_crm_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'creator');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_user');
    }

    public function forceDelete(User $user, User $record): bool
    {
        if (! $user->can('force_delete_crm_user')) {
            return false;
        }

        if ($user->id === $record->id) {
            return false;
        }

        return $this->hasAccess($user, $record, 'creator');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_user');
    }

    public function restore(User $user, User $record): bool
    {
        if (! $user->can('restore_crm_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'creator');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_user');
    }
}
