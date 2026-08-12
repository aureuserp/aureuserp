<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\WorkCenterLossType;
use Webkul\Security\Models\User;

class WorkCenterLossTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_work::center::loss::type');
    }

    public function view(User $user, WorkCenterLossType $workCenterLossType): bool
    {
        return $user->can('view_manufacturing_work::center::loss::type');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_work::center::loss::type');
    }

    public function update(User $user, WorkCenterLossType $workCenterLossType): bool
    {
        return $user->can('update_manufacturing_work::center::loss::type');
    }

    public function delete(User $user, WorkCenterLossType $workCenterLossType): bool
    {
        return $user->can('delete_manufacturing_work::center::loss::type');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_work::center::loss::type');
    }

    public function forceDelete(User $user, WorkCenterLossType $workCenterLossType): bool
    {
        return $user->can('force_delete_manufacturing_work::center::loss::type');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_manufacturing_work::center::loss::type');
    }

    public function restore(User $user, WorkCenterLossType $workCenterLossType): bool
    {
        return $user->can('restore_manufacturing_work::center::loss::type');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_manufacturing_work::center::loss::type');
    }
}
