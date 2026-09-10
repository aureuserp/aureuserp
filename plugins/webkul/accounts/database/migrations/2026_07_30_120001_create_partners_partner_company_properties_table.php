<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners_partner_company_properties', function (Blueprint $table) {
            $table->id();

            $table->foreignId('partner_id')
                ->comment('Partner')
                ->constrained('partners_partners')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->comment('Company')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('property_account_payable_id')
                ->nullable()
                ->comment('Payable Account')
                ->constrained(table: 'accounts_accounts', indexName: 'partner_company_props_payable_foreign')
                ->nullOnDelete();

            $table->foreignId('property_account_receivable_id')
                ->nullable()
                ->comment('Receivable Account')
                ->constrained(table: 'accounts_accounts', indexName: 'partner_company_props_receivable_foreign')
                ->nullOnDelete();

            $table->foreignId('property_account_position_id')
                ->nullable()
                ->comment('Fiscal Position')
                ->constrained(table: 'accounts_fiscal_positions', indexName: 'partner_company_props_position_foreign')
                ->nullOnDelete();

            $table->foreignId('property_payment_term_id')
                ->nullable()
                ->comment('Customer Payment Term')
                ->constrained(table: 'accounts_payment_terms', indexName: 'partner_company_props_payment_term_foreign')
                ->nullOnDelete();

            $table->foreignId('property_supplier_payment_term_id')
                ->nullable()
                ->comment('Vendor Payment Term')
                ->constrained(table: 'accounts_payment_terms', indexName: 'partner_company_props_supplier_term_foreign')
                ->nullOnDelete();

            $table->foreignId('property_inbound_payment_method_line_id')
                ->nullable()
                ->comment('Inbound Payment Method Line')
                ->constrained(table: 'accounts_payment_method_lines', indexName: 'partner_company_props_inbound_line_foreign')
                ->nullOnDelete();

            $table->foreignId('property_outbound_payment_method_line_id')
                ->nullable()
                ->comment('Outbound Payment Method Line')
                ->constrained(table: 'accounts_payment_method_lines', indexName: 'partner_company_props_outbound_line_foreign')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['partner_id', 'company_id'], 'partner_company_props_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners_partner_company_properties');
    }
};
