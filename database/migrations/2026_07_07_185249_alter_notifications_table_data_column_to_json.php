<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('notifications')->where('data', '')->update(['data' => '{}']);

        db_dialect()->alterColumnType('notifications', 'data', 'json', 'jsonb', 'data::jsonb');
    }

    public function down(): void
    {
        db_dialect()->alterColumnType('notifications', 'data', 'text', 'text', 'data::text');
    }
};
