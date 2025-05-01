<?php

namespace Webkul\Account\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class IncotermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts_incoterms')->delete();

        $user = User::first();

        $incoterms = [
            ['code' => 'EXW', 'name' => 'EX WORKS', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FCA', 'name' => 'FREE CARRIER', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FAS', 'name' => 'FREE ALONGSIDE SHIP', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FOB', 'name' => 'FREE ON BOARD', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CFR', 'name' => 'COST AND FREIGHT', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CIF', 'name' => 'COST, INSURANCE AND FREIGHT', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CPT', 'name' => 'CARRIAGE PAID TO', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CIP', 'name' => 'CARRIAGE AND INSURANCE PAID TO', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DPU', 'name' => 'DELIVERED AT PLACE UNLOADED', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DAP', 'name' => 'DELIVERED AT PLACE', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DDP', 'name' => 'DELIVERED DUTY PAID', 'creator_id' => $user?->id, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('accounts_incoterms')->insert($incoterms);
    }
}
