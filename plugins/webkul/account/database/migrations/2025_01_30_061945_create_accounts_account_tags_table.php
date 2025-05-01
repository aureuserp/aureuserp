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
        Schema::create('accounts_account_tags', function (Blueprint $table) {
            $table->id();
            $table->string('color')->nullable()->comment('Color');
            $table->foreignId('country_id')->nullable()->comment('Country ID')->constrained('countries')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator ID')->constrained('users')->nullOnDelete();
            $table->string('applicability')->comment('Applicability');
            $table->string('name')->comment('Name');
            $table->boolean('tax_negate')->default(false)->comment('Tax Negate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_account_tags');
    }
};
