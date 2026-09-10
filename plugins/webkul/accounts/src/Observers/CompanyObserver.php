<?php

namespace Webkul\Account\Observers;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Models\MoveLine;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;

class CompanyObserver
{
    public function updating(Company $company): void
    {
        if (! Package::isPluginInstalled('accounts')) {
            return;
        }

        if (! $company->isDirty('currency_id')) {
            return;
        }

        if (! $this->hasAccountingEntries($company)) {
            return;
        }

        throw ValidationException::withMessages([
            'data.currency_id' => __('accounts::observers/company.currency-change'),
        ]);
    }

    protected function hasAccountingEntries(Company $company): bool
    {
        return MoveLine::withoutGlobalScope(CompanyScope::class)
            ->whereIn('company_id', $this->companyTreeIds($company))
            ->exists();
    }

    protected function companyTreeIds(Company $company): array
    {
        $rootId = $company->id;

        $parentId = $company->parent_id;

        while ($parentId) {
            $rootId = $parentId;

            $parentId = Company::withoutGlobalScopes()->whereKey($parentId)->value('parent_id');
        }

        return $this->descendantIds($rootId)->push($rootId)->unique()->all();
    }

    protected function descendantIds(int $companyId): Collection
    {
        return Company::withoutGlobalScopes()
            ->where('parent_id', $companyId)
            ->pluck('id')
            ->reduce(
                fn (Collection $ids, int $branchId) => $ids
                    ->push($branchId)
                    ->merge($this->descendantIds($branchId)),
                collect(),
            );
    }
}
