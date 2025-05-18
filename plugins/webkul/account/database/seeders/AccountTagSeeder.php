<?php

namespace Webkul\Account\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class AccountTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts_account_tags')->delete();

        $user = User::first();

        $accountTags = [
            [
                'color'         => '#FF0000',
                'country_id'    => 1,
                'creator_id'    => $user?->id,
                'applicability' => 'accounts',
                'name'          => 'Operating Activities',
                'tax_negate'    => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'color'         => '#00FF00',
                'country_id'    => 1,
                'creator_id'    => $user?->id,
                'applicability' => 'accounts',
                'name'          => 'Financing Activities',
                'tax_negate'    => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'color'         => '#0000FF',
                'country_id'    => 1,
                'creator_id'    => $user?->id,
                'applicability' => 'accounts',
                'name'          => 'Investing & Extraordinary Activities',
                'tax_negate'    => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'color'         => '#FFFF00',
                'country_id'    => 1,
                'creator_id'    => $user?->id,
                'applicability' => 'accounts',
                'name'          => 'Demo Capital Account',
                'tax_negate'    => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'color'         => '#FF00FF',
                'country_id'    => 1,
                'creator_id'    => $user?->id,
                'applicability' => 'accounts',
                'name'          => 'Demo Stock Account',
                'tax_negate'    => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'color'         => '#00FFFF',
                'country_id'    => 1,
                'creator_id'    => $user?->id,
                'applicability' => 'accounts',
                'name'          => 'Demo Sale of Land Account',
                'tax_negate'    => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'color'         => '#000000',
                'country_id'    => 1,
                'creator_id'    => $user?->id,
                'applicability' => 'accounts',
                'name'          => 'Demo CEO Wages Account',
                'tax_negate'    => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'color'         => '#FFFFFF',
                'country_id'    => 1,
                'creator_id'    => $user?->id,
                'applicability' => 'accounts',
                'name'          => 'Office Furniture',
                'tax_negate'    => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('accounts_account_tags')->insert($accountTags);
    }
}
