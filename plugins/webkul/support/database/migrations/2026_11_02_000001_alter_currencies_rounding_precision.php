<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->decimal('rounding', 12, 6)->default(0.01)->change();
        });

        DB::table('currencies')
            ->where('rounding', '<=', 0)
            ->orderBy('id')
            ->get(['id', 'decimal_places'])
            ->each(fn (object $currency) => DB::table('currencies')
                ->where('id', $currency->id)
                ->update(['rounding' => number_format(10 ** -max(0, (int) $currency->decimal_places), 6, '.', '')]));
    }

    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->decimal('rounding', 8, 2)->default(0.00)->change();
        });
    }
};
