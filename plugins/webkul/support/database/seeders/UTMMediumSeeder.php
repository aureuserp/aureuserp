<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UTMMediumSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('utm_mediums')->delete();

        $mediums = [
            'Phone',
            'Direct',
            'Email',
            'Banner',
            'X',
            'Facebook',
            'LinkedIn',
            'Television',
            'Google',
        ];

        DB::table('utm_mediums')->insert(collect($mediums)->map(function ($medium) {
            return [
                'name'       => $medium,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray());
    }
}
