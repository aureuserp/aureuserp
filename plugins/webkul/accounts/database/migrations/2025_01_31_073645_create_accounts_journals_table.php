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
        Schema::create('accounts_journals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('default_account_id')
                ->nullable()
                ->comment('Default Account')
                ->constrained('accounts_accounts')
                ->restrictOnDelete();
            $table->foreignId('suspense_account_id')
                ->nullable()
                ->comment('Suspense Account')
                ->constrained('accounts_accounts')
                ->restrictOnDelete();
            $table->integer('sort')->nullable()->comment('Sort Order');
            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Currency')
                ->constrained('currencies')
                ->nullOnDelete();
            $table->foreignId('company_id')
                ->nullable()
                ->comment('Company')
                ->constrained('companies')
                ->restrictOnDelete();
            $table->foreignId('profit_account_id')
                ->nullable()
                ->comment('Profit Account')
                ->constrained('accounts_accounts')
                ->nullOnDelete();
            $table->foreignId('loss_account_id')
                ->nullable()
                ->comment('Loss Account')
                ->constrained('accounts_accounts')
                ->nullOnDelete();
            $table->foreignId('bank_account_id')
                ->nullable()
                ->comment('Bank Account')
                ->constrained('banks')
                ->restrictOnDelete();
            $table->foreignId('creator_id')
                ->nullable()
                ->comment('Creator')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('color')->nullable()->comment('Color');
            $table->string('access_token')->nullable()->comment('Access Token');
            $table->string('code')->nullable()->comment('Code');
            $table->string('type')->comment('Type');
            $table->string('invoice_reference_type')->comment('Communication Type');
            $table->string('invoice_reference_model')->comment('Communication Standard');
            $table->string('bank_statements_source')->nullable()->comment('Bank Statements Source');
            $table->string('name')->comment('Name');
            $table->text('order_override_regex')->nullable()->comment('Sequence Override Regex');
            $table->boolean('auto_check_on_post')->nullable()->default(false)->comment('Auto Check on Post');
            $table->boolean('restrict_mode_hash_table')->nullable()->default(false)->comment('Restrict Mode Hash Table');
            $table->boolean('refund_order')->nullable()->default(false)->comment('Refund Order');
            $table->boolean('payment_order')->nullable()->default(false)->comment('Payment Order');
            $table->boolean('show_on_dashboard')->nullable()->default(false)->comment('Show on Dashboard');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_journals');
    }
};
