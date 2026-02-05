<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounts_partial_reconciles', function (Blueprint $table) {
            $table->dropForeign(['debit_move_id']);
            $table->dropForeign(['credit_move_id']);

            $table->foreign('debit_move_id')
                ->references('id')
                ->on('accounts_account_move_lines')
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->foreign('credit_move_id')
                ->references('id')
                ->on('accounts_account_move_lines')
                ->restrictOnDelete()
                ->restrictOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('accounts_partial_reconciles', function (Blueprint $table) {
            $table->dropForeign(['debit_move_id']);
            $table->dropForeign(['credit_move_id']);

            $table->foreign('debit_move_id')
                ->references('id')
                ->on('accounts_account_moves')
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->foreign('credit_move_id')
                ->references('id')
                ->on('accounts_account_moves')
                ->restrictOnDelete()
                ->restrictOnUpdate();
        });
    }
};
