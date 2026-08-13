<?php

namespace Webkul\Maintenance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Maintenance\Models\EquipmentCategory;
use Webkul\Security\Models\User;

class EquipmentCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_maintenance_equipment::category');
    }

    public function view(User $user, EquipmentCategory $category): bool
    {
        return $user->can('view_maintenance_equipment::category');
    }

    public function create(User $user): bool
    {
        return $user->can('create_maintenance_equipment::category');
    }

    public function update(User $user, EquipmentCategory $category): bool
    {
        return $user->can('update_maintenance_equipment::category');
    }

    public function delete(User $user, EquipmentCategory $category): bool
    {
        return $user->can('delete_maintenance_equipment::category');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_maintenance_equipment::category');
    }
}
