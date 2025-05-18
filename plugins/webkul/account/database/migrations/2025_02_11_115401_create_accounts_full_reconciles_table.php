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
        Schema::create('accounts_full_reconciles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exchange_move_id')->nullable()->constrained('accounts_account_moves')->nullOnDelete();
            $table->foreignId('created_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_account_move_lines', function (Blueprint $table) {
            if (Schema::hasColumn('accounts_account_move_lines', 'full_reconcile_id')) {
                $table->dropForeign(['full_reconcile_id']);
            }
        });

        Schema::dropIfExists('accounts_full_reconciles');
    }
};
