<?php

namespace Webkul\Accounting\Filament\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasCompanyScope
{
    private static function getAccessibleCompanyIds(): array
    {
        /** @var \Webkul\Security\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $companyIds = $user->allowedCompanies()
            ->pluck('companies.id')
            ->all();

        if ($user->default_company_id) {
            $companyIds[] = $user->default_company_id;
        }

        return [
            ...array_unique(
                array_filter($companyIds)
            ),
        ];
    }
}
