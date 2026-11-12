<?php

namespace Webkul\Account\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class TaxGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $company = Company::first();

        $now = Carbon::now();

        $taxGroups = [
            [
                'id'                 => 1,
                'sort'               => 1,
                'company_id'         => $company?->id,
                'country_id'         => 104,
                'creator_id'         => $user?->id,
                'key'                => 'tax-group.tax-15',
                'name'               => __('accounts::seeders.tax-groups.tax-15'),
                'preceding_subtotal' => null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
        ];

        seed_rows('accounts_tax_groups', $taxGroups);
    }
}
