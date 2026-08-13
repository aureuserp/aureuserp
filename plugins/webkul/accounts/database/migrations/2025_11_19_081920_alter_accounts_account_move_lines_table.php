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
        $foreignKeys = collect(Schema::getForeignKeys('accounts_partial_reconciles'))
            ->pluck('name');

        Schema::table('accounts_partial_reconciles', function (Blueprint $table) use ($foreignKeys) {
            if ($foreignKeys->contains('accounts_partial_reconciles_debit_move_id_foreign')) {
                $table->dropForeign(['debit_move_id']);
            }

            if ($foreignKeys->contains('accounts_partial_reconciles_credit_move_id_foreign')) {
                $table->dropForeign(['credit_move_id']);
            }
        });
    }
};
