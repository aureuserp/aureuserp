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
        Schema::create('accounts_fiscal_position_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fiscal_position_id')
                ->comment('Fiscal Position')
                ->constrained('accounts_fiscal_positions')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->comment('Company')
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('account_source_id')
                ->comment('Account Source')
                ->constrained('accounts_accounts')
                ->restrictOnDelete();

            $table->foreignId('account_destination_id')
                ->nullable()
                ->comment('Account Destination')
                ->constrained('accounts_accounts')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->comment('Creator')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_fiscal_position_accounts');
    }
};
