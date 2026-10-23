<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\Tag;
use Webkul\Security\Models\User;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_tag');
    }

    public function view(User $user, Tag $tag): bool
    {
        return $user->can('view_crm_tag');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_tag');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->can('update_crm_tag');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->can('delete_crm_tag');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_tag');
    }
}
