<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\WorkCenterProductivityLoss;
use Webkul\Security\Models\User;

class WorkCenterProductivityLossPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_work::center::productivity::loss');
    }

    public function view(User $user, WorkCenterProductivityLoss $workCenterProductivityLoss): bool
    {
        return $user->can('view_manufacturing_work::center::productivity::loss');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_work::center::productivity::loss');
    }

    public function update(User $user, WorkCenterProductivityLoss $workCenterProductivityLoss): bool
    {
        return $user->can('update_manufacturing_work::center::productivity::loss');
    }

    public function delete(User $user, WorkCenterProductivityLoss $workCenterProductivityLoss): bool
    {
        return $user->can('delete_manufacturing_work::center::productivity::loss');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_work::center::productivity::loss');
    }

    public function forceDelete(User $user, WorkCenterProductivityLoss $workCenterProductivityLoss): bool
    {
        return $user->can('force_delete_manufacturing_work::center::productivity::loss');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_manufacturing_work::center::productivity::loss');
    }

    public function restore(User $user, WorkCenterProductivityLoss $workCenterProductivityLoss): bool
    {
        return $user->can('restore_manufacturing_work::center::productivity::loss');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_manufacturing_work::center::productivity::loss');
    }
}
