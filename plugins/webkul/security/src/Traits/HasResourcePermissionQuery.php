<?php

namespace Webkul\Security\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasResourcePermissionQuery
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        return static::getModel()::applyPermissionScope($query);
    }
}
