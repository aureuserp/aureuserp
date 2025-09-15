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
        Schema::table('accounts_payment_terms', function (Blueprint $table) {
            $table->text('note')->nullable()->comment('Note')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_payment_terms', function (Blueprint $table) {
            $table->string('note')->nullable()->comment('Note')->change();
        });
    }
};
