<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('accounts_accounts', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('creator_id')
                    ->comment('Parent Account')
                    ->constrained('accounts_accounts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounts_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts_accounts', 'parent_id')) {
                $table->dropForeign(['parent_id']);

                $table->dropColumn('parent_id');
            }
        });
    }
};
