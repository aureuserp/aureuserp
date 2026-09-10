<?php

namespace Webkul\Support\Traits;

use Webkul\Support\Models\Scopes\AllowedCompanyScope;

trait RestrictToAllowedCompanies
{
    public static function bootRestrictToAllowedCompanies(): void
    {
        static::addGlobalScope(new AllowedCompanyScope);
    }
}
