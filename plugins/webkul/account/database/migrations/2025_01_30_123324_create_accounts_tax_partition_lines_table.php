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
        Schema::create('accounts_tax_partition_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->comment('Account')->constrained('accounts_accounts')->nullOnDelete();
            $table->foreignId('tax_id')->nullable()->comment('Tax')->constrained('accounts_taxes')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator')->constrained('users')->nullOnDelete();
            $table->integer('sort')->nullable()->comment('Sort Order');
            $table->string('repartition_type')->comment('Repartition Type');
            $table->string('document_type')->comment('Document Type');
            $table->string('use_in_tax_closing')->nullable()->comment('Use in Tax Closing');
            $table->decimal('factor', 15, 4)->nullable()->comment('Factor')->default(0);
            $table->decimal('factor_percent')->nullable()->comment('Factor Percent')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_tax_partition_lines');
    }
};
