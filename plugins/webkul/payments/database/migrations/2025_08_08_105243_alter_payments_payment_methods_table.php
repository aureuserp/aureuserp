<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments_payment_methods', function (Blueprint $table) {
            $table->dropForeign(['created_by']);

            $table->renameColumn('created_by', 'creator_id');

            $table->foreign('creator_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments_payment_methods', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);

            $table->renameColumn('creator_id', 'created_by');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};
