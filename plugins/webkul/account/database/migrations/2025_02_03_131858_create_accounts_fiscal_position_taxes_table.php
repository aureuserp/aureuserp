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
        Schema::create('accounts_fiscal_position_taxes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fiscal_position_id')->comment('Fiscal Position')->constrained('accounts_fiscal_positions')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->nullOnDelete();
            $table->foreignId('tax_source_id')->comment('Tax Source')->constrained('accounts_taxes')->restrictOnDelete();
            $table->foreignId('tax_destination_id')->nullable()->comment('Tax Destination')->constrained('accounts_taxes')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator')->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_fiscal_position_taxes');
    }
};
