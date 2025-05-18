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
        Schema::create('accounts_accounts_move_reversals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->comment('Company')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator')->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable()->comment('Reason displayed on Credit Note');
            $table->date('date')->comment('Date of Reversal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_accounts_move_reversals');
    }
};
