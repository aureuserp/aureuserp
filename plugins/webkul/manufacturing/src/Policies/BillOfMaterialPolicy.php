<?php

namespace Webkul\Manufacturing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Security\Models\User;

class BillOfMaterialPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'view_any');
    }

    public function view(User $user, BillOfMaterial $billOfMaterial): bool
    {
        return $this->check($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'create');
    }

    public function update(User $user, BillOfMaterial $billOfMaterial): bool
    {
        return $this->check($user, 'update');
    }

    public function delete(User $user, BillOfMaterial $billOfMaterial): bool
    {
        return $this->check($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->check($user, 'delete_any');
    }

    public function forceDelete(User $user, BillOfMaterial $billOfMaterial): bool
    {
        return $this->check($user, 'force_delete');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->check($user, 'force_delete_any');
    }

    public function restore(User $user, BillOfMaterial $billOfMaterial): bool
    {
        return $this->check($user, 'restore');
    }

    public function restoreAny(User $user): bool
    {
        return $this->check($user, 'restore_any');
    }

    protected function check(User $user, string $ability): bool
    {
        return $user->can("{$ability}_manufacturing_bill::of::material")
            || $user->can("{$ability}_manufacturing_bills::of::material");
    }
}
