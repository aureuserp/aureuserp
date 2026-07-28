<?php

namespace Webkul\Manufacturing\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkCenterProductivityLossSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('manufacturing_work_center_productivity_losses')->upsert([
            [
                'id'           => 1,
                'sort'         => 1,
                'loss_type'    => 'availability',
                'name'         => 'Material Availability',
                'manual'       => true,
                'loss_type_id' => 1,
                'creator_id'   => 1,
                'created_at'   => '2026-04-24 05:46:53',
                'updated_at'   => '2026-04-24 05:46:53',
            ],
            [
                'id'           => 2,
                'sort'         => 2,
                'loss_type'    => 'availability',
                'name'         => 'Equipment Failure',
                'manual'       => true,
                'loss_type_id' => 1,
                'creator_id'   => 1,
                'created_at'   => '2026-04-24 05:46:53',
                'updated_at'   => '2026-04-24 05:46:53',
            ],
            [
                'id'           => 3,
                'sort'         => 3,
                'loss_type'    => 'availability',
                'name'         => 'Setup and Adjustments',
                'manual'       => true,
                'loss_type_id' => 1,
                'creator_id'   => 1,
                'created_at'   => '2026-04-24 05:46:53',
                'updated_at'   => '2026-04-24 05:46:53',
            ],
            [
                'id'           => 4,
                'sort'         => 5,
                'loss_type'    => 'performance',
                'name'         => 'Reduced Speed',
                'manual'       => false,
                'loss_type_id' => 2,
                'creator_id'   => 1,
                'created_at'   => '2026-04-24 05:46:53',
                'updated_at'   => '2026-04-24 05:46:53',
            ],
            [
                'id'           => 5,
                'sort'         => 6,
                'loss_type'    => 'quality',
                'name'         => 'Process Defect',
                'manual'       => true,
                'loss_type_id' => 3,
                'creator_id'   => 1,
                'created_at'   => '2026-04-24 05:46:53',
                'updated_at'   => '2026-04-24 05:46:53',
            ],
            [
                'id'           => 6,
                'sort'         => 7,
                'loss_type'    => 'quality',
                'name'         => 'Reduced Yield',
                'manual'       => true,
                'loss_type_id' => 3,
                'creator_id'   => 1,
                'created_at'   => '2026-04-24 05:46:53',
                'updated_at'   => '2026-04-24 05:46:53',
            ],
            [
                'id'           => 7,
                'sort'         => 0,
                'loss_type'    => 'productive',
                'name'         => 'Fully Productive Time',
                'manual'       => false,
                'loss_type_id' => 4,
                'creator_id'   => 1,
                'created_at'   => '2026-04-24 05:46:53',
                'updated_at'   => '2026-04-24 05:46:53',
            ],
        ], ['id'], ['sort', 'loss_type', 'name', 'manual', 'loss_type_id', 'creator_id', 'updated_at']);
    }
}
