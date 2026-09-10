<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('partners_partners')
            ->whereNotNull('company_id')
            ->whereNull('user_id')
            ->whereNotIn(
                'id',
                DB::table('users')->whereNotNull('partner_id')->select('partner_id')
            )
            ->update(['company_id' => null]);
    }

    public function down(): void {}
};
