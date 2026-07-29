<?php

namespace Webkul\Maintenance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Webkul\Maintenance\Models\Stage;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::parse('2026-05-18 06:05:20.17115');

        $stages = [
            [
                'id'         => 1,
                'sort'       => 1,
                'creator_id' => 1,
                'name'       => 'New Request',
                'done'       => false,
            ],
            [
                'id'         => 2,
                'sort'       => 2,
                'creator_id' => 1,
                'name'       => 'In Progress',
                'done'       => false,
            ],
            [
                'id'         => 3,
                'sort'       => 3,
                'creator_id' => 1,
                'name'       => 'Repaired',
                'done'       => true,
            ],
            [
                'id'         => 4,
                'sort'       => 4,
                'creator_id' => 1,
                'name'       => 'Scrap',
                'done'       => true,
            ],
        ];

        foreach ($stages as $stage) {
            Stage::query()->updateOrCreate(
                [
                    'id' => $stage['id'],
                ],
                [
                    'sort'       => $stage['sort'],
                    'creator_id' => $stage['creator_id'],
                    'name'       => $stage['name'],
                    'done'       => $stage['done'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
            );
        }
    }
}
