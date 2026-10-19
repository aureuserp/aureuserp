<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class LostReasonSeeder extends Seeder
{
    /**
     * Seed the default lost reasons without duplicating them on reinstall.
     */
    public function run(): void
    {
        $creator = User::find(1);

        foreach (['Too expensive', 'Not enough stock', 'No resources or skills available'] as $name) {
            DB::table('crm_lost_reasons')->updateOrInsert(
                ['name' => $name],
                [
                    'creator_id' => $creator?->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
