<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Account\Enums;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('accounts_account_moves')->whereNull('is_storno')->update(['is_storno' => 0]);
        DB::table('accounts_account_moves')->whereNull('always_tax_exigible')->update(['always_tax_exigible' => 0]);
        DB::table('accounts_account_moves')->whereNull('checked')->update(['checked' => 0]);
        DB::table('accounts_account_moves')->whereNull('posted_before')->update(['posted_before' => 0]);
        DB::table('accounts_account_moves')->whereNull('made_sequence_gap')->update(['made_sequence_gap' => 0]);
        DB::table('accounts_account_moves')->whereNull('is_manually_modified')->update(['is_manually_modified' => 0]);
        DB::table('accounts_account_moves')->whereNull('is_move_sent')->update(['is_move_sent' => 0]);

        Schema::table('accounts_account_moves', function (Blueprint $table) {
            $table->string('state')->default(Enums\MoveState::DRAFT)->comment('State')->change();
            $table->string('payment_state')->default(Enums\PaymentState::NOT_PAID)->nullable()->comment('Payment State')->change();

            $table->boolean('is_storno')->default(0)->nullable(false)->change();
            $table->boolean('always_tax_exigible')->default(0)->nullable(false)->change();
            $table->boolean('checked')->default(0)->nullable(false)->change();
            $table->boolean('posted_before')->default(0)->nullable(false)->change();
            $table->boolean('made_sequence_gap')->default(0)->nullable(false)->change();
            $table->boolean('is_manually_modified')->default(0)->nullable(false)->change();
            $table->boolean('is_move_sent')->default(0)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_account_moves', function (Blueprint $table) {
            $table->string('state')->default(null)->comment('State')->change();
            $table->string('payment_state')->default(null)->nullable()->comment('Payment State');

            $table->boolean('is_storno')->nullable()->default(null)->change();
            $table->boolean('always_tax_exigible')->nullable()->default(null)->change();
            $table->boolean('checked')->nullable()->default(null)->change();
            $table->boolean('posted_before')->nullable()->default(null)->change();
            $table->boolean('made_sequence_gap')->nullable()->default(null)->change();
            $table->boolean('is_manually_modified')->nullable()->default(null)->change();
            $table->boolean('is_move_sent')->nullable()->default(null)->change();
        });
    }
};
