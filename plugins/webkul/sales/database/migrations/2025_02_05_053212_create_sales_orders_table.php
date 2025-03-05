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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utm_source_id')->nullable()->comment('UTM source')->constrained('utm_sources')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->comment('Campaign')->constrained('utm_campaigns')->nullOnDelete();
            $table->foreignId('medium_id')->nullable()->comment('Recruitments utm sources')->constrained('utm_mediums')->nullOnDelete();
            $table->foreignId('company_id')->comment('Company')->constrained('companies')->restrictOnDelete();
            $table->foreignId('partner_id')->comment('Partner')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('journal_id')->nullable()->comment('Invoicing Journal')->constrained('accounts_journals')->nullOnDelete();
            $table->foreignId('partner_invoice_id')->comment('Invoice Address')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('partner_shipping_id')->comment('Shipping Address')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('fiscal_position_id')->nullable()->comment('Fiscal Position')->constrained('accounts_fiscal_positions')->nullOnDelete();
            $table->foreignId('payment_term_id')->nullable()->comment('Payment Term')->constrained('accounts_payment_terms')->nullOnDelete();
            $table->foreignId('currency_id')->comment('Currency')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->comment('Salesperson')->constrained('users')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->comment('Sales Team')->constrained('sales_teams')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Created by')->constrained('users')->nullOnDelete();
            $table->foreignId('sale_order_template_id')->nullable()->comment('Order Template')->constrained('sales_order_templates')->nullOnDelete();
            $table->string('access_token')->nullable()->comment('Access Token');
            $table->string('name')->comment('Order Reference');
            $table->string('state')->nullable()->comment('Status');
            $table->string('client_order_ref')->nullable()->comment('Customer Reference');
            $table->string('origin')->nullable()->comment('Source Document');
            $table->string('reference')->nullable()->comment('Payment Reference');
            $table->string('signed_by')->nullable()->comment('Signed By');
            $table->string('invoice_status')->nullable()->comment('Invoice Status');
            $table->date('validity_date')->nullable()->comment('Expiration Date');
            $table->text('note')->nullable()->comment('Terms and conditions');
            $table->decimal('currency_rate', 15, 4)->nullable()->comment('Currency Rate')->default(0);
            $table->decimal('amount_untaxed', 15, 4)->nullable()->comment('Untaxed Amount')->default(0);
            $table->decimal('amount_tax', 15, 4)->nullable()->comment('Taxes')->default(0);
            $table->decimal('amount_total', 15, 4)->nullable()->comment('Total')->default(0);
            $table->boolean('locked')->default(false)->comment('Locked');
            $table->boolean('require_signature')->default(false)->comment('Require Signature');
            $table->boolean('require_payment')->default(false)->comment('Require Payment');
            $table->date('commitment_date')->nullable()->comment('Commitment Date');
            $table->date('date_order')->comment('Order Date');
            $table->date('signed_on')->nullable()->comment('Signed On');
            $table->decimal('prepayment_percent', 15, 4)->nullable()->comment('Prepayment Percentage')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
