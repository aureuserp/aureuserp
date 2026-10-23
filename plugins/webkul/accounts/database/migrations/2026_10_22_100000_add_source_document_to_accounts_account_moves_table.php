<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_account_moves', function (Blueprint $table) {
            $table->string('source_document_type', 100)->nullable()->comment('Source Document Type');
            $table->string('source_document_key', 191)->nullable()->comment('Source Document Key');

            $table->unique(['company_id', 'source_document_type', 'source_document_key'], 'accounts_account_moves_source_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('accounts_account_moves', 'source_document_key')) {
            return;
        }

        Schema::table('accounts_account_moves', function (Blueprint $table) {
            $table->dropUnique('accounts_account_moves_source_unique');

            $table->dropColumn(['source_document_type', 'source_document_key']);
        });
    }
};
