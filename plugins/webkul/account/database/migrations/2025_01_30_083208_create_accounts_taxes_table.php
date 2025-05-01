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
        Schema::create('accounts_taxes', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable()->comment('Sort Order');
            $table->foreignId('company_id')->comment('Company')->constrained('companies')->restrictOnDelete();
            $table->foreignId('tax_group_id')->comment('Tax Group')->constrained('accounts_tax_groups')->restrictOnDelete();
            $table->foreignId('cash_basis_transition_account_id')->nullable()->comment('Cash Basis Transition Account')->constrained('accounts_accounts')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->comment('Country')->constrained('countries')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator')->constrained('users')->nullOnDelete();
            $table->string('type_tax_use')->comment('Tax Use');
            $table->string('tax_scope')->comment('Tax Scope')->nullable();
            $table->string('formula')->comment('Formula')->nullable();
            $table->string('amount_type')->comment('Amount Type');
            $table->string('price_include_override')->comment('Price Include Override')->nullable();
            $table->string('tax_exigibility')->comment('Tax Exigibility')->nullable();
            $table->string('name')->comment('Name')->nullable();
            $table->string('description')->comment('Description')->nullable();
            $table->string('invoice_label')->comment('Invoice Label')->nullable();
            $table->text('invoice_legal_notes')->comment('Invoice Legal Notes')->nullable();
            $table->decimal('amount', 15, 4)->comment('Amount')->nullable()->default(0);
            $table->boolean('is_active')->default(0)->comment('Active')->nullable();
            $table->boolean('include_base_amount')->default(0)->comment('Include Base Amount')->nullable();
            $table->boolean('is_base_affected')->default(0)->comment('Base Affected')->nullable();
            $table->boolean('analytic')->default(0)->comment('Analytic')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_taxes');
    }
};
