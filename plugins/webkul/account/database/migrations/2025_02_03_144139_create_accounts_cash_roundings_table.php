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
        Schema::create('accounts_cash_roundings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->nullable()->comment('Created By')->constrained('users')->nullOnDelete();
            $table->string('strategy')->comment('Rounding Strategy');
            $table->string('rounding_method')->comment('Rounding Method');
            $table->string('name')->comment('Name');
            $table->decimal('rounding', 15, 4)->comment('Rounding')->default(0);
            $table->foreignId('profit_account_id')->nullable()->comment('Profit Account')->constrained('accounts_accounts')->nullOnDelete();
            $table->foreignId('loss_account_id')->nullable()->comment('Loss Account')->constrained('accounts_accounts')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_cash_roundings');
    }
};
