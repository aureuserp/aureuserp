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
        $root = $company;

        while ($root->parent) {
            $root = $root->parent;
        }

        return $this->descendantIds($root)->push($root->id)->unique()->all();
    }

    protected function descendantIds(Company $company): Collection
    {
        return $company->branches->reduce(
            fn (Collection $ids, Company $branch) => $ids
                ->push($branch->id)
                ->merge($this->descendantIds($branch)),
            collect(),
        );
    }
}
