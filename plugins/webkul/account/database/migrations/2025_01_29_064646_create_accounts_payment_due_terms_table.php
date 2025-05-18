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
        Schema::create('accounts_payment_due_terms', function (Blueprint $table) {
            $table->id();
            $table->integer('nb_days')->nullable()->comment('Number of Days');
            $table->foreignId('payment_id')->nullable()->comment('Payment Terms')->constrained('accounts_payment_terms')->cascadeOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator')->constrained('users')->nullOnDelete();
            $table->string('value')->comment('Value');
            $table->string('delay_type')->comment('Delay Type');
            $table->string('days_next_month')->nullable()->comment('Days Next Month');
            $table->decimal('value_amount', 15, 4)->nullable()->comment('Value Amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_payment_due_terms');
    }
};
