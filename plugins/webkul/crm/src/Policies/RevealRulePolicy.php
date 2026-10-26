<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\RevealRule;
use Webkul\Security\Models\User;

class RevealRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_reveal::rule');
    }

    public function view(User $user, RevealRule $revealRule): bool
    {
        return $user->can('view_crm_reveal::rule');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_reveal::rule');
    }

    public function update(User $user, RevealRule $revealRule): bool
    {
        return $user->can('update_crm_reveal::rule');
    }

    public function delete(User $user, RevealRule $revealRule): bool
    {
        return $user->can('delete_crm_reveal::rule');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_reveal::rule');
    }

    public function forceDelete(User $user, RevealRule $revealRule): bool
    {
        return $user->can('force_delete_crm_reveal::rule');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_reveal::rule');
    }

    public function restore(User $user, RevealRule $revealRule): bool
    {
        return $user->can('restore_crm_reveal::rule');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_reveal::rule');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_crm_reveal::rule');
    }
}
