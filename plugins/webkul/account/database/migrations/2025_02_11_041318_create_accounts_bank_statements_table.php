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
        Schema::create('accounts_bank_statements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('journal_id')->nullable()->constrained('accounts_journals')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name')->nullable()->comment('Reference');
            $table->string('reference')->nullable()->comment('External Reference');
            $table->string('first_line_index')->nullable()->comment('First Line Index');
            $table->date('date')->nullable()->comment('Date');
            $table->decimal('balance_start', 15, 4)->default(0)->comment('Starting Balance');
            $table->decimal('balance_end', 15, 4)->default(0)->comment('Ending Balance');
            $table->decimal('balance_end_real', 15, 4)->default(0)->comment('Real Ending Balance');
            $table->boolean('is_completed')->default(0)->comment('Is Completed');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_bank_statements');
    }
};
