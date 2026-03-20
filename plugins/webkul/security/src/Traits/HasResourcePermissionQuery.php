<?php

namespace Webkul\Security\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasResourcePermissionQuery
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (method_exists($query->getModel(), 'scopeApplyPermissionScope')) {
            return $query->applyPermissionScope();
        }

        return $query;
    }
}
