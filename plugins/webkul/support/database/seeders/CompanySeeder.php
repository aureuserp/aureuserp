<?php

namespace Webkul\Support\Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $this->seedDefaultCompany();
        } catch (Throwable $e) {
            $this->command?->warn('Skipping default company seeding: '.$e->getMessage());
        }
    }

    /**
     * Seed the default company and its partner.
     */
    protected function seedDefaultCompany(): void
    {
        DB::transaction(function () {
            if (
                ! Schema::hasTable('users')
                || ! Schema::hasTable('companies')
                || ! Schema::hasTable('partners_partners')
            ) {
                throw new Exception('Required tables are missing.');
            }

            DB::table('partners_partners')->delete();
            DB::table('companies')->delete();
            DB::table('users')->delete();

            $currency = Currency::resolveDefault();

            if (! $currency) {
                throw new Exception('No currency is available to assign to the default company.');
            }

            Company::create([
                'sort'                => 1,
                'name'                => 'My Company',
                'tax_id'              => 'MYC123456',
                'registration_number' => 'MYCREG001',
                'company_id'          => 'MYCOMP001',
                'email'               => 'info@mycompany.local',
                'phone'               => '1234567890',
                'mobile'              => '1234567890',
                'color'               => '#AAAAAA',
                'is_active'           => true,
                'founded_date'        => '2000-01-01',
                'currency_id'         => $currency->id,
                'website'             => 'https://mycompany.local',
            ]);
        });
    }
}
