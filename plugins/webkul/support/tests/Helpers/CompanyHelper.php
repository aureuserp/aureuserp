<?php

use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/SecurityHelper.php';

class CompanyHelper
{
    public static function company(array $overrides = []): Company
    {
        return Company::factory()->create($overrides);
    }

    /**
     * Authenticate a user restricted to the given companies, with the company
     * scope ACTIVE (no bypass). $companies may be a Company, an id, or an array.
     */
    public static function actingAsCompanyUser($companies, array $permissions = [], ?array $activeIds = null): User
    {
        $ids = collect(is_array($companies) ? $companies : [$companies])
            ->map(fn ($company) => $company instanceof Company ? $company->id : $company)
            ->all();

        $user = SecurityHelper::authenticateWithPermissions($permissions, bypassCompanyScope: false);

        $user->forceFill(['default_company_id' => $ids[0] ?? null])->saveQuietly();

        $user->allowedCompanies()->sync($ids);

        session([CompanyContext::SESSION_KEY => $activeIds ?? $ids]);

        app()->forgetInstance(CompanyContext::class);

        return $user;
    }

    /**
     * Run the callback as a console/seeder would: nobody authenticated and no
     * active company, so current_company_id() resolves to null.
     */
    public static function withoutCompanyContext(callable $callback): mixed
    {
        $active = session(CompanyContext::SESSION_KEY);

        session()->forget(CompanyContext::SESSION_KEY);

        app()->instance(CompanyContext::class, new class extends CompanyContext
        {
            public function activeIds(): array
            {
                return [];
            }

            public function currentId(): ?int
            {
                return null;
            }
        });

        try {
            return $callback();
        } finally {
            if ($active !== null) {
                session([CompanyContext::SESSION_KEY => $active]);
            }

            app()->forgetInstance(CompanyContext::class);
        }
    }

    public static function setActive(array $ids): void
    {
        session([CompanyContext::SESSION_KEY => $ids]);

        app()->forgetInstance(CompanyContext::class);
    }
}
