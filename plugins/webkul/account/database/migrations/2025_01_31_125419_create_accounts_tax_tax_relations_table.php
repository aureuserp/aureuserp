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
        Schema::create('accounts_tax_taxes', function (Blueprint $table) {
            $table->foreignId('parent_tax_id')->constrained('accounts_taxes')->onDelete('cascade');
            $table->foreignId('child_tax_id')->constrained('accounts_taxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_tax_taxes');
    }
};
