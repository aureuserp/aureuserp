<?php

namespace Webkul\PluginManager\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\PluginManager\Models\Plugin;
use Webkul\Security\Models\User;

class PluginPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Plugin $plugin): bool
    {
        return $user->can('view_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Plugin $plugin): bool
    {
        return $user->can('update_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Plugin $plugin): bool
    {
        return $user->can('delete_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Plugin $plugin): bool
    {
        return $user->can('force_delete_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Plugin $plugin): bool
    {
        return $user->can('restore_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_plugin_manager_plugin');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_plugin_manager_plugin');
    }
}
