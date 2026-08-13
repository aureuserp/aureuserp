<?php

namespace Webkul\Maintenance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Security\Models\User;

class EquipmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_maintenance_equipment');
    }

    public function view(User $user, Equipment $equipment): bool
    {
        return $user->can('view_maintenance_equipment');
    }

    public function create(User $user): bool
    {
        return $user->can('create_maintenance_equipment');
    }

    public function update(User $user, Equipment $equipment): bool
    {
        return $user->can('update_maintenance_equipment');
    }

    public function delete(User $user, Equipment $equipment): bool
    {
        return $user->can('delete_maintenance_equipment');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_maintenance_equipment');
    }

    public function forceDelete(User $user, Equipment $equipment): bool
    {
        return $user->can('force_delete_maintenance_equipment');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_maintenance_equipment');
    }

    public function restore(User $user, Equipment $equipment): bool
    {
        return $user->can('restore_maintenance_equipment');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_maintenance_equipment');
    }
}
