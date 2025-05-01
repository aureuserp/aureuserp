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
        Schema::create('accounts_accounts_move_line_taxes', function (Blueprint $table) {
            $table->foreignId('move_line_id')
                ->constrained('accounts_account_move_lines')
                ->cascadeOnDelete();

            $table->foreignId('tax_id')
                ->constrained('accounts_taxes')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_accounts_move_line_taxes');
    }
};
