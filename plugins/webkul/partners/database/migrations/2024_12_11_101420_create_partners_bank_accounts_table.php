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
        Schema::create('partners_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->string('account_holder_name');
            $table->boolean('is_active')->default(1);
            $table->boolean('can_send_money')->default(0);

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('partner_id')
                ->constrained('partners_partners')
                ->cascadeOnDelete();

            $table->foreignId('bank_id')
                ->constrained('banks')
                ->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners_bank_accounts');
    }
};
