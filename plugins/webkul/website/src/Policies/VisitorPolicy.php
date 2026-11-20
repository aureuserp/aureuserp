<?php

namespace Webkul\Website\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Website\Models\Visitor;

class VisitorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_website_visitor');
    }

    public function view(User $user, Visitor $visitor): bool
    {
        return $user->can('view_website_visitor');
    }

    public function delete(User $user, Visitor $visitor): bool
    {
        return $user->can('delete_website_visitor');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_website_visitor');
    }
}
