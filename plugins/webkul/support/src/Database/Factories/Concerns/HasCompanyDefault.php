<?php

namespace Webkul\Support\Database\Factories\Concerns;

use Webkul\Support\Models\Company;

trait HasCompanyDefault
{
    public function configure(): static
    {
        return $this->afterMaking(function ($model): void {
            if (! empty($model->company_id)) {
                return;
            }

            if (method_exists($model, 'autoAssignsCompany') && ! $model::autoAssignsCompany()) {
                return;
            }

            $model->company_id = current_company_id()
                ?? Company::query()->value('id')
                ?? Company::factory()->create()->id;
        });
    }

    public function company(Company|int $company): static
    {
        return $this->state([
            'company_id' => $company instanceof Company ? $company->id : $company,
        ]);
    }
}
