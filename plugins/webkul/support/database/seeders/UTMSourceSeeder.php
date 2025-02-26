<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UTMSourceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('utm_sources')->delete();

        $sources = [
            'Search engine',
            'Lead Recall',
            'Newsletter',
            'Facebook',
            'X',
            'LinkedIn',
            'Monster',
            'Glassdoor',
            'Craigslist',
        ];

        DB::table('utm_sources')->insert(collect($sources)->map(function ($medium) {
            return [
                'name'       => $medium,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray());
    }
}
