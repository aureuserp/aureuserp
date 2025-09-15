<?php

namespace Webkul\Security\Models;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    public function getNameAttribute($value)
    {
        return Str::ucfirst($value);
    }
}
