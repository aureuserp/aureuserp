<?php

namespace Webkul\Support\Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
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

            $user = User::first();

            $partnerId = DB::table('partners_partners')->insertGetId([
                'sub_type'         => 'company',
                'company_registry' => 'MYCREG001',
                'name'             => 'My Company',
                'email'            => 'info@mycompany.local',
                'website'          => 'https://mycompany.local',
                'tax_id'           => 'MYC123456',
                'phone'            => '1234567890',
                'mobile'           => '1234567890',
                'creator_id'       => $user?->id,
                'color'            => '#AAAAAA',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $currency = Currency::find(1);

            if (! $currency) {
                throw new Exception('Currency with ID 1 not found.');
            }

            DB::table('companies')->insert([
                'sort'                => 1,
                'name'                => 'My Company',
                'tax_id'              => 'MYC123456',
                'registration_number' => 'MYCREG001',
                'company_id'          => 'MYCOMP001',
                'creator_id'          => $user?->id,
                'email'               => 'info@mycompany.local',
                'phone'               => '1234567890',
                'mobile'              => '1234567890',
                'color'               => '#AAAAAA',
                'is_active'           => true,
                'founded_date'        => '2000-01-01',
                'currency_id'         => $currency->id,
                'website'             => 'https://mycompany.local',
                'partner_id'          => $partnerId,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
        }
    }
}
