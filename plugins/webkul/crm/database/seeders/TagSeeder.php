<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::find(1);

        $tags = [
            'Product'    => '#6366f1',
            'Software'   => '#0ea5e9',
            'Services'   => '#10b981',
            'Training'   => '#f59e0b',
            'Consulting' => '#ef4444',
            'Upsell'     => '#8b5cf6',
        ];

        foreach ($tags as $name => $color) {
            if (DB::table('crm_tags')->where('name', $name)->exists()) {
                continue;
            }

            DB::table('crm_tags')->insert([
                'name'       => $name,
                'color'      => $color,
                'creator_id' => $creator?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
