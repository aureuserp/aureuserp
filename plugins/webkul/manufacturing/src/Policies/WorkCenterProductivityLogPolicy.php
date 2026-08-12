<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\WorkCenterProductivityLog;
use Webkul\Security\Models\User;

class WorkCenterProductivityLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_work::center::productivity::log');
    }

    public function view(User $user, WorkCenterProductivityLog $workCenterProductivityLog): bool
    {
        return $user->can('view_manufacturing_work::center::productivity::log');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_work::center::productivity::log');
    }

    public function update(User $user, WorkCenterProductivityLog $workCenterProductivityLog): bool
    {
        return $user->can('update_manufacturing_work::center::productivity::log');
    }

    public function delete(User $user, WorkCenterProductivityLog $workCenterProductivityLog): bool
    {
        return $user->can('delete_manufacturing_work::center::productivity::log');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_work::center::productivity::log');
    }

    public function forceDelete(User $user, WorkCenterProductivityLog $workCenterProductivityLog): bool
    {
        return $user->can('force_delete_manufacturing_work::center::productivity::log');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_manufacturing_work::center::productivity::log');
    }

    public function restore(User $user, WorkCenterProductivityLog $workCenterProductivityLog): bool
    {
        return $user->can('restore_manufacturing_work::center::productivity::log');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_manufacturing_work::center::productivity::log');
    }
}
