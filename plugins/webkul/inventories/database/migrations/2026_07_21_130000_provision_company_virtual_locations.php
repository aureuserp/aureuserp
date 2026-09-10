<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Webkul\Inventory\Services\CompanyLocationProvisioner;
use Webkul\Support\Models\Company;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inventories_locations') || ! Schema::hasTable('companies')) {
            return;
        }

        $provisioner = app(CompanyLocationProvisioner::class);

        Company::query()
            ->orderBy('id')
            ->each(fn (Company $company) => $provisioner->provision($company));
    }

    public function down(): void {}
};
