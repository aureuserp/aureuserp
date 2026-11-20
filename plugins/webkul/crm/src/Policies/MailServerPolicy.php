<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\MailServer;
use Webkul\Security\Models\User;

class MailServerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_mail::server');
    }

    public function view(User $user, MailServer $mailServer): bool
    {
        return $user->can('view_crm_mail::server');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_mail::server');
    }

    public function update(User $user, MailServer $mailServer): bool
    {
        return $user->can('update_crm_mail::server');
    }

    public function delete(User $user, MailServer $mailServer): bool
    {
        return $user->can('delete_crm_mail::server');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_mail::server');
    }
}
