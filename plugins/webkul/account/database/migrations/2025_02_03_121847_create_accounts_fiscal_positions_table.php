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
        Schema::create('accounts_fiscal_positions', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->comment('Sort Order');
            $table->foreignId('company_id')->comment('Company')->constrained('companies')->restrictOnDelete();
            $table->foreignId('country_id')->nullable()->comment('Country')->constrained('countries')->nullOnDelete();
            $table->foreignId('country_group_id')->nullable()->comment('Country Group')->constrained('countries')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator')->constrained('users')->nullOnDelete();
            $table->string('zip_from')->nullable()->comment('Zip From');
            $table->string('zip_to')->nullable()->comment('Zip To');
            $table->string('foreign_vat')->nullable()->comment('Foreign VAT');
            $table->string('name')->comment('Name');
            $table->text('notes')->nullable()->comment('Notes');
            $table->boolean('auto_reply')->default(false)->comment('Auto Reply');
            $table->boolean('vat_required')->default(false)->comment('VAT Required');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_fiscal_positions');
    }
};
