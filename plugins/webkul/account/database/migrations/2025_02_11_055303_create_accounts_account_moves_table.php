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
        Schema::create('accounts_account_moves', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->comment('Sort order');
            $table->foreignId('journal_id')->nullable()->comment('Journal')->constrained('accounts_journals')->restrictOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->nullOnDelete();
            $table->foreignId('tax_cash_basis_origin_move_id')->nullable()->comment('Cash Basis Origin')->constrained('accounts_account_moves')->nullOnDelete();
            $table->foreignId('auto_post_origin_id')->nullable()->comment('Auto Post Origin')->constrained('accounts_account_moves')->nullOnDelete();
            $table->foreignId('origin_payment_id')->nullable()->comment('Payment')->constrained('accounts_account_payments')->nullOnDelete();
            $table->integer('secure_sequence_number')->nullable()->comment('Secure Sequence Number');
            $table->foreignId('invoice_payment_term_id')->nullable()->comment('Payment Term')->constrained('accounts_payment_terms')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->comment('Partner')->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('commercial_partner_id')->nullable()->comment('Commercial Partner')->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('partner_shipping_id')->nullable()->comment('Shipping Address')->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('partner_bank_id')->nullable()->comment('Bank Account')->constrained('partners_bank_accounts')->nullOnDelete();
            $table->foreignId('fiscal_position_id')->nullable()->comment('Fiscal Position')->constrained('accounts_fiscal_positions')->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->comment('Currency')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('reversed_entry_id')->nullable()->comment('Reversed Entry')->constrained('accounts_account_moves')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->comment('Campaign')->constrained('utm_campaigns')->nullOnDelete();
            $table->foreignId('invoice_user_id')->nullable()->comment('Invoice User')->constrained('users')->nullOnDelete();
            $table->foreignId('statement_line_id')->nullable()->constrained('accounts_bank_statement_lines')->nullOnDelete();
            $table->foreignId('invoice_incoterm_id')->nullable()->comment('Incoterm')->constrained('accounts_incoterms')->nullOnDelete();
            $table->foreignId('preferred_payment_method_line_id')->nullable()->comment('Payment Method Line')->constrained('accounts_payment_method_lines')->nullOnDelete();
            $table->foreignId('invoice_cash_rounding_id')->nullable()->comment('Cash Rounding')->constrained('accounts_cash_roundings')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Created By')->constrained('users')->nullOnDelete();
            $table->string('sequence_prefix')->nullable()->comment('Sequence Prefix');
            $table->string('access_token')->nullable()->comment('Access Token');
            $table->string('name')->nullable()->comment('Name');
            $table->string('reference')->nullable()->comment('Reference');
            $table->string('state')->comment('State');
            $table->string('move_type')->comment('Move Type');
            $table->boolean('auto_post')->comment('Auto Post')->default(0);
            $table->string('inalterable_hash')->nullable()->comment('Inalterable Hash');
            $table->string('payment_reference')->nullable()->comment('Payment Reference');
            $table->string('qr_code_method')->nullable()->comment('QR Code Method');
            $table->string('payment_state')->nullable()->comment('Payment State');
            $table->string('invoice_source_email')->nullable()->comment('Source Email');
            $table->string('invoice_partner_display_name')->nullable()->comment('Partner Display Name');
            $table->string('invoice_origin')->nullable()->comment('Origin');
            $table->string('incoterm_location')->nullable()->comment('Incoterm Location');
            $table->date('date')->comment('Date');
            $table->date('auto_post_until')->nullable()->comment('Auto Post Until');
            $table->date('invoice_date')->nullable()->comment('Invoice Date');
            $table->date('invoice_date_due')->nullable()->comment('Due Date');
            $table->date('delivery_date')->nullable()->comment('Delivery Date');
            $table->json('sending_data')->nullable()->comment('Sending Data');
            $table->text('narration')->nullable()->comment('Narration');
            $table->decimal('invoice_currency_rate', 15, 4)->nullable()->comment('Currency Rate');
            $table->decimal('amount_untaxed', 15, 4)->nullable()->comment('Untaxed Amount');
            $table->decimal('amount_tax', 15, 4)->nullable()->comment('Tax Amount');
            $table->decimal('amount_total', 15, 4)->nullable()->comment('Total Amount');
            $table->decimal('amount_residual', 15, 4)->nullable()->comment('Residual Amount');
            $table->decimal('amount_untaxed_signed', 15, 4)->nullable()->comment('Untaxed Amount Signed');
            $table->decimal('amount_untaxed_in_currency_signed', 15, 4)->nullable()->comment('Untaxed Amount in Currency Signed');
            $table->decimal('amount_tax_signed', 15, 4)->nullable()->comment('Tax Amount Signed');
            $table->decimal('amount_total_signed', 15, 4)->nullable()->comment('Total Amount Signed');
            $table->decimal('amount_total_in_currency_signed', 15, 4)->nullable()->comment('Total Amount in Currency Signed');
            $table->decimal('amount_residual_signed', 15, 4)->nullable()->comment('Residual Amount Signed');
            $table->decimal('quick_edit_total_amount', 15, 4)->nullable()->comment('Quick Edit Total Amount');
            $table->boolean('is_storno')->nullable()->comment('Is Storno');
            $table->boolean('always_tax_exigible')->nullable()->comment('Always Tax Exigible');
            $table->boolean('checked')->nullable()->comment('Checked');
            $table->boolean('posted_before')->nullable()->comment('Posted Before');
            $table->boolean('made_sequence_gap')->nullable()->comment('Made Sequence Gap');
            $table->boolean('is_manually_modified')->nullable()->comment('Is Manually Modified');
            $table->boolean('is_move_sent')->nullable()->comment('Is Move Sent');
            $table->foreignId('source_id')->nullable()->comment('Source')->constrained('utm_sources')->nullOnDelete();
            $table->foreignId('medium_id')->nullable()->comment('Medium')->constrained('utm_mediums')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_bank_statement_lines', function (Blueprint $table) {
            if (Schema::hasColumn('accounts_bank_statement_lines', 'move_id')) {
                $table->dropForeign(['move_id']);
            }
        });

        Schema::dropIfExists('accounts_account_moves');
    }
};
