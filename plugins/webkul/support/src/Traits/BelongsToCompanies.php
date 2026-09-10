<?php

namespace Webkul\Support\Traits;

use Webkul\Support\Models\Scopes\CompaniesScope;

trait BelongsToCompanies
{
    public static function bootBelongsToCompanies(): void
    {
        static::addGlobalScope(new CompaniesScope);
    }

    public function companyScopeRelation(): string
    {
        return 'companies';
    }
}
