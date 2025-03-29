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
        Schema::create('accounts_payment_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->nullOnDelete();
            $table->integer('sort')->nullable()->comment('Sort');
            $table->integer('discount_days')->nullable()->comment('Discount Days');
            $table->foreignId('creator_id')->nullable()->comment('Creator')->constrained('users')->nullOnDelete();
            $table->string('early_pay_discount')->nullable()->comment('Cash Discount Tax Reduction');
            $table->string('name')->comment('Name');
            $table->string('note')->nullable()->comment('Note');
            $table->boolean('display_on_invoice')->default(false)->nullable()->comment('Display on Invoice');
            $table->boolean('early_discount')->default(false)->nullable()->comment('Early Discount');
            $table->decimal('discount_percentage', 15, 4)->nullable()->comment('Discount Percentage')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_payment_terms');
    }
};
