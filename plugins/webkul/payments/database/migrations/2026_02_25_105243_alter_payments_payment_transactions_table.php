<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments_payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['created_id']);

            $table->renameColumn('created_id', 'creator_id');

            $table->foreign('creator_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments_payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);

            $table->renameColumn('creator_id', 'created_id');

            $table->foreign('created_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};
