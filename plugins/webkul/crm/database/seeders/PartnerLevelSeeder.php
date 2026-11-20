<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class PartnerLevelSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::find(1);

        $levels = [
            'Gold'   => 3,
            'Silver' => 2,
            'Bronze' => 1,
        ];

        $sort = 0;

        foreach ($levels as $name => $weight) {
            $sort++;

            if (DB::table('crm_partner_levels')->where('name', $name)->exists()) {
                continue;
            }

            DB::table('crm_partner_levels')->insert([
                'name'           => $name,
                'partner_weight' => $weight,
                'sort'           => $sort,
                'creator_id'     => $creator?->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
