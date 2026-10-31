<?php

namespace Webkul\Website\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Support\Models\UTMMedium;
use Webkul\Website\Services\ContactFormHandler;

class UTMMediumSeeder extends Seeder
{
    public function run(): void
    {
        UTMMedium::query()->firstOrCreate(['name' => ContactFormHandler::LEAD_MEDIUM]);
    }
}
