<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('currency_rates', function (Blueprint $table) {
            $table->date('name')->change();
        });
    }

    public function down()
    {
        Schema::table('currency_rates', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
};
