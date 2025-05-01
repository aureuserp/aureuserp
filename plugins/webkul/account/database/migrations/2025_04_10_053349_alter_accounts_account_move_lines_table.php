<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Account\Enums;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('accounts_account_move_lines')->whereNull('is_imported')->update(['is_imported' => 0]);
        DB::table('accounts_account_move_lines')->whereNull('tax_tag_invert')->update(['tax_tag_invert' => 0]);
        DB::table('accounts_account_move_lines')->whereNull('reconciled')->update(['reconciled' => 0]);
        DB::table('accounts_account_move_lines')->whereNull('is_downpayment')->update(['is_downpayment' => 0]);

        Schema::table('accounts_account_move_lines', function (Blueprint $table) {
            $table->string('display_type')->default(Enums\DisplayType::PRODUCT)->comment('Display Type')->nullable()->change();

            $table->boolean('is_imported')->default(0)->nullable(false)->change();
            $table->boolean('tax_tag_invert')->default(0)->nullable(false)->change();
            $table->boolean('reconciled')->default(0)->nullable(false)->change();
            $table->boolean('is_downpayment')->default(0)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_account_move_lines', function (Blueprint $table) {
            $table->string('display_type')->default(null)->comment('Display Type')->nullable()->change();

            $table->boolean('is_imported')->nullable()->default(null)->change();
            $table->boolean('tax_tag_invert')->nullable()->default(null)->change();
            $table->boolean('reconciled')->nullable()->default(null)->change();
            $table->boolean('is_downpayment')->nullable()->default(null)->change();
        });
    }
};
