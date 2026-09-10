<?php

namespace Webkul\Inventory\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Webkul\Inventory\Services\CompanyLocationProvisioner;
use Webkul\Support\Models\Company;

class CompanyObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Company $company): void
    {
        app(CompanyLocationProvisioner::class)->provision($company);
    }
}
