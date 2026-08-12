<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('utm_campaigns')
            ->whereNotNull('company_id')
            ->update(['company_id' => null]);
    }

    public function down(): void {}
};
