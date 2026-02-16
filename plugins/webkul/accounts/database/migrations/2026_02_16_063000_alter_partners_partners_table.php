<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('partners_partners', function (Blueprint $table) {
            $table->dropForeign('fk_partners_account_position');

            $table->foreign('property_account_position_id', 'fk_partners_fiscal_position')
                ->references('id')
                ->on('accounts_fiscal_positions')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners_partners', function (Blueprint $table) {
            $table->dropForeign('fk_partners_fiscal_position');

            $table->foreign('property_account_position_id', 'fk_partners_account_position')
                ->references('id')
                ->on('accounts_accounts')
                ->nullOnDelete();
        });
    }
};
