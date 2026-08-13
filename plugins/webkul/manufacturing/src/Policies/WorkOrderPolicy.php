<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Security\Models\User;

class WorkOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_manufacturing_work::order');
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('view_manufacturing_work::order');
    }

    public function create(User $user): bool
    {
        return $user->can('create_manufacturing_work::order');
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('update_manufacturing_work::order');
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('delete_manufacturing_work::order');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_manufacturing_work::order');
    }
}
