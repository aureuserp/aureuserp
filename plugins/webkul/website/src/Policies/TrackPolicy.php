<?php

namespace Webkul\Website\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Website\Models\Track;

class TrackPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_website_track');
    }

    public function view(User $user, Track $track): bool
    {
        return $user->can('view_website_track');
    }

    public function delete(User $user, Track $track): bool
    {
        return $user->can('delete_website_track');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_website_track');
    }
}
