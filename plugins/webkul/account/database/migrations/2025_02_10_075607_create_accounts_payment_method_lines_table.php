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
        Schema::create('accounts_payment_method_lines', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->comment('Sort order');
            $table->foreignId('payment_method_id')->nullable()->comment('Payment Method')->constrained('accounts_payment_methods')->restrictOnDelete();
            $table->foreignId('payment_account_id')->nullable()->comment('Payment Account')->constrained('accounts_accounts')->restrictOnDelete();
            $table->foreignId('journal_id')->nullable()->comment('Journal')->constrained('accounts_journals')->restrictOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Users')->constrained('users')->nullOnDelete();
            $table->string('name')->nullable()->comment('Name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_payment_method_lines');
    }
};
