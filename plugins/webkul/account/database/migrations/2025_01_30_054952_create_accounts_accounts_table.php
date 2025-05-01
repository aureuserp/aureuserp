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
        Schema::create('accounts_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->nullable()->comment('Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator')->constrained('users')->nullOnDelete();
            $table->string('account_type')->comment('Account Type');
            $table->string('name')->comment('Name');
            $table->string('code')->nullable()->comment('Code');
            $table->string('note')->nullable()->comment('Note');
            $table->boolean('deprecated')->nullable()->comment('Deprecated');
            $table->boolean('reconcile')->nullable()->comment('Reconcile');
            $table->boolean('non_trade')->nullable()->comment('Non Trade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_journals', function (Blueprint $table) {
            if (Schema::hasColumn('accounts_journals', 'loss_account_id')) {
                $table->dropForeign(['loss_account_id']);
            }

            if (Schema::hasColumn('accounts_journals', 'profit_account_id')) {
                $table->dropForeign(['profit_account_id']);
            }

            if (Schema::hasColumn('accounts_journals', 'default_account_id')) {
                $table->dropForeign(['default_account_id']);
            }

            if (Schema::hasColumn('accounts_journals', 'suspense_account_id')) {
                $table->dropForeign(['suspense_account_id']);
            }
        });

        Schema::dropIfExists('accounts_accounts');
    }
};
