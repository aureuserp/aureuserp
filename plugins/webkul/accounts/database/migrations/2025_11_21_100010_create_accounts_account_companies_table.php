<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_account_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
                ->constrained('accounts_accounts')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['account_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_account_companies');
    }
};
