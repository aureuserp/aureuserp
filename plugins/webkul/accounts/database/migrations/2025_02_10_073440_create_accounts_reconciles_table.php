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
        Schema::create('accounts_reconciles', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->comment('Sort Order')->nullable();
            $table->foreignId('company_id')->comment('Company')->constrained('companies')->restrictOnDelete();
            $table->integer('past_months_limit')->comment('Search Month Limit')->nullable();
            $table->foreignId('created_by')->nullable()->comment('Created By')->constrained('users')->nullOnDelete();
            $table->string('rule_type')->comment('Type');
            $table->string('matching_order')->comment('Matching Order');
            $table->string('counter_part_type')->comment('Counter Part Type')->nullable();
            $table->string('match_nature')->comment('Amount Type')->nullable();
            $table->string('match_amount')->nullable()->comment('Amount Condition')->nullable();
            $table->string('match_label')->comment('Label')->nullable();
            $table->string('match_level_parameters')->comment('Level Parameters')->nullable();
            $table->string('match_note')->comment('Note')->nullable();
            $table->string('match_note_parameters')->comment('Note Parameters')->nullable();
            $table->string('match_transaction_type')->comment('Transaction Type')->nullable();
            $table->string('match_transaction_type_parameters')->comment('Transaction Type Parameters')->nullable();
            $table->string('payment_tolerance_type')->comment('Payment Tolerance Type')->nullable();
            $table->string('decimal_separator')->comment('Decimal Separator')->nullable();
            $table->string('name')->comment('Name');
            $table->boolean('auto_reconcile')->comment('Auto Validate');
            $table->boolean('to_check')->comment('To Check')->default(false);
            $table->boolean('match_text_location_label')->comment('Match Text Location Label')->default(false);
            $table->boolean('match_text_location_note')->comment('Match Text Location Note')->default(false);
            $table->boolean('match_text_location_reference')->comment('Match Text Location Reference')->default(false);
            $table->boolean('match_same_currency')->comment('Match Same Currency')->default(false);
            $table->boolean('allow_payment_tolerance')->comment('Allow Payment Tolerance')->default(false);
            $table->boolean('match_partner')->comment('Match Partner')->default(false);
            $table->decimal('match_amount_min', 15, 4)->comment('Amount Min')->nullable();
            $table->decimal('match_amount_max', 15, 4)->comment('Amount Max')->nullable();
            $table->decimal('payment_tolerance_parameters', 15, 4)->comment('Payment Tolerance Parameters')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_reconciles');
    }
};
