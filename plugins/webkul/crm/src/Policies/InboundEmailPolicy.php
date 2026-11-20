<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\InboundEmail;
use Webkul\Security\Models\User;

class InboundEmailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_inbound::email');
    }

    public function view(User $user, InboundEmail $inboundEmail): bool
    {
        return $user->can('view_crm_inbound::email');
    }

    public function delete(User $user, InboundEmail $inboundEmail): bool
    {
        return $user->can('delete_crm_inbound::email');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_inbound::email');
    }
}
