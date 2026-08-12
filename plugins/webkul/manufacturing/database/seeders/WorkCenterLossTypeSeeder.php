<?php

namespace Webkul\Manufacturing\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkCenterLossTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('manufacturing_work_center_loss_types')->upsert([
            [
                'id'         => 1,
                'loss_type'  => 'availability',
                'creator_id' => 1,
                'created_at' => '2026-04-24 05:46:53',
                'updated_at' => '2026-04-24 05:46:53',
            ],
            [
                'id'         => 2,
                'loss_type'  => 'performance',
                'creator_id' => 1,
                'created_at' => '2026-04-24 05:46:53',
                'updated_at' => '2026-04-24 05:46:53',
            ],
            [
                'id'         => 3,
                'loss_type'  => 'quality',
                'creator_id' => 1,
                'created_at' => '2026-04-24 05:46:53',
                'updated_at' => '2026-04-24 05:46:53',
            ],
            [
                'id'         => 4,
                'loss_type'  => 'productive',
                'creator_id' => 1,
                'created_at' => '2026-04-24 05:46:53',
                'updated_at' => '2026-04-24 05:46:53',
            ],
        ], ['id'], ['loss_type', 'creator_id', 'updated_at']);
    }
}
