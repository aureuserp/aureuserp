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
            $table->integer('message_bounce')->nullable()->comment('Message Bounce');
            $table->integer('supplier_rank')->nullable()->comment('Supplier Rank');
            $table->integer('customer_rank')->nullable()->comment('Customer Rank');
            $table->string('invoice_warning')->nullable()->comment('Invoice');
            $table->string('autopost_bills')->nullable()->comment('Auto post bills');
            $table->string('credit_limit')->nullable()->comment('Credit Limits');
            $table->string('ignore_abnormal_invoice_date')->nullable()->comment('Ignore Abnormal Invoice Date');
            $table->string('ignore_abnormal_invoice_amount')->nullable()->comment('Ignore abnormal Invoice amount');
            $table->string('invoice_sending_method')->nullable()->comment('Invoice Sending');
            $table->string('invoice_edi_format_store')->nullable()->comment('Invoice Edi Format Store');
            $table->integer('trust')->nullable()->comment('Degree of trust you have in this debtor');
            $table->integer('invoice_warn_msg')->nullable()->comment('Message for Invoice');
            $table->decimal('debit_limit', 16, 2)->nullable()->comment('Debit Limit');
            $table->string('peppol_endpoint')->nullable()->comment('Peppol Endpoint');
            $table->string('peppol_eas')->nullable()->comment('Peppol EAS');
            $table->string('sale_warn')->nullable()->comment('Sale Warning');
            $table->string('sale_warn_msg')->nullable()->comment('Sale Warning Message');
            $table->text('comment')->nullable()->comment('Comment');

            $table->foreignId('property_account_payable_id')->nullable()->comment('Account Payable');
            $table->foreignId('property_account_receivable_id')->nullable()->comment('Account Receivable');
            $table->foreignId('property_account_position_id')->nullable()->comment('Account Position');
            $table->foreignId('property_payment_term_id')->nullable()->comment('Payment Term');
            $table->foreignId('property_supplier_payment_term_id')->nullable()->comment('Supplier payment term');
            $table->foreignId('property_inbound_payment_method_line_id')->nullable()->comment('Property Inbound Payment Method Line');
            $table->foreignId('property_outbound_payment_method_line_id')->nullable()->comment('Property Outbound Payment Method Line');

            $table->foreign('property_account_payable_id', 'fk_partners_account_payable')
                ->references('id')
                ->on('accounts_accounts')
                ->nullOnDelete();

            $table->foreign('property_account_receivable_id', 'fk_partners_account_receivable')
                ->references('id')
                ->on('accounts_accounts')
                ->nullOnDelete();

            $table->foreign('property_account_position_id', 'fk_partners_account_position')
                ->references('id')
                ->on('accounts_accounts')
                ->nullOnDelete();

            $table->foreign('property_payment_term_id', 'fk_partners_payment_term')
                ->references('id')
                ->on('accounts_payment_terms')
                ->nullOnDelete();

            $table->foreign('property_supplier_payment_term_id', 'fk_partners_supplier_payment_term')
                ->references('id')
                ->on('accounts_payment_terms')
                ->nullOnDelete();

            $table->foreign('property_inbound_payment_method_line_id', 'fk_partners_inbound_payment_method')
                ->references('id')
                ->on('accounts_payment_method_lines')
                ->nullOnDelete();

            $table->foreign('property_outbound_payment_method_line_id', 'fk_partners_outbound_payment_method')
                ->references('id')
                ->on('accounts_payment_method_lines')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners_partners', function (Blueprint $table) {
            $table->dropForeign('fk_partners_account_payable');
            $table->dropForeign('fk_partners_account_receivable');
            $table->dropForeign('fk_partners_account_position');
            $table->dropForeign('fk_partners_payment_term');
            $table->dropForeign('fk_partners_supplier_payment_term');
            $table->dropForeign('fk_partners_inbound_payment_method');
            $table->dropForeign('fk_partners_outbound_payment_method');

            $table->dropColumn([
                'message_bounce',
                'supplier_rank',
                'customer_rank',
                'invoice_warning',
                'autopost_bills',
                'credit_limit',
                'ignore_abnormal_invoice_date',
                'ignore_abnormal_invoice_amount',
                'invoice_sending_method',
                'invoice_edi_format_store',
                'trust',
                'invoice_warn_msg',
                'debit_limit',
                'peppol_endpoint',
                'peppol_eas',
                'sale_warn',
                'sale_warn_msg',
                'comment',
                'property_account_payable_id',
                'property_account_receivable_id',
                'property_account_position_id',
                'property_payment_term_id',
                'property_supplier_payment_term_id',
                'property_inbound_payment_method_line_id',
                'property_outbound_payment_method_line_id',
            ]);
        });
    }
};
