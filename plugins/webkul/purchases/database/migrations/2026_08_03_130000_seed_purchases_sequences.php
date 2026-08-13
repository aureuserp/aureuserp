<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Webkul\Purchase\Database\Seeders\SequenceSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('sequences')) {
            return;
        }

        (new SequenceSeeder)->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
