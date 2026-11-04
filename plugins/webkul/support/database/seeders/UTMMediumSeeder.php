<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

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
            'Website',
        ];

        $user = User::first();

        DB::table('utm_mediums')->insert(collect($mediums)->map(function ($medium) use ($user) {
            return [
                'name'       => $medium,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray());
    }
}
