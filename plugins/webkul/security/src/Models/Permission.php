<?php

namespace Webkul\Security\Models;

use Spatie\Permission\Models\Permission as BasePermission;
use Webkul\Security\PermissionRegistrar;
use Illuminate\Database\Eloquent\Collection;

class Permission extends BasePermission
{
    /**
     * Get the current cached permissions.
     */
    protected static function getPermissions(array $params = [], bool $onlyOne = false): Collection
    {
        return app(PermissionRegistrar::class)
            ->setPermissionClass(static::class)
            ->getPermissions($params, $onlyOne);
    }
}
