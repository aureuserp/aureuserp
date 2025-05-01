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
        Schema::create('accounts_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('Code');
            $table->string('payment_type')->comment('Payment Type');
            $table->string('name')->comment('Name');
            $table->foreignId('created_by')->nullable()->comment('Created By')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_payment_methods');
    }
};
