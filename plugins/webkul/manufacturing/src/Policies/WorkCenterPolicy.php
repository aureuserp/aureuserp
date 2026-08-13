<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Security\Models\User;

class WorkCenterPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_work::center');
    }

    public function view(User $user, WorkCenter $workCenter): bool
    {
        return $user->can('view_manufacturing_work::center');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_work::center');
    }

    public function update(User $user, WorkCenter $workCenter): bool
    {
        return $user->can('update_manufacturing_work::center');
    }

    public function delete(User $user, WorkCenter $workCenter): bool
    {
        return $user->can('delete_manufacturing_work::center');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_work::center');
    }

    public function forceDelete(User $user, WorkCenter $workCenter): bool
    {
        return $user->can('force_delete_manufacturing_work::center');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_manufacturing_work::center');
    }

    public function restore(User $user, WorkCenter $workCenter): bool
    {
        return $user->can('restore_manufacturing_work::center');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_manufacturing_work::center');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_manufacturing_work::center');
    }
}
