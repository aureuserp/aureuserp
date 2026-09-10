<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounts_payment_terms')) {
            return;
        }

        DB::table('accounts_payment_terms')->update(['company_id' => null]);
    }

    public function down(): void {}
};
