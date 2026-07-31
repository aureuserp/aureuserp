<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        db_dialect()->alterColumnType('currency_rates', 'name', 'date', 'date', 'name::date');
    }

    public function down()
    {
        db_dialect()->alterColumnType('currency_rates', 'name', 'string', 'varchar(255)', 'name::text');
    }
};
