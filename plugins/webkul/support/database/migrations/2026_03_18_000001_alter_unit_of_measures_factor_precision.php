<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('unit_of_measures', function (Blueprint $table) {
            $table->double('factor')->nullable()->default(0)->change();
        });

        $uomFactors = [
            'Units'      => 1.0,
            'Dozens'     => 0.08333333333333333,
            'kg'         => 1.0,
            'g'          => 1000.0,
            't'          => 0.001,
            'lb'         => 2.20462,
            'oz'         => 35.274,
            'Days'       => 8.0,
            'Hours'      => 1.0,
            'm'          => 1.0,
            'mm'         => 1000.0,
            'km'         => 0.001,
            'cm'         => 100.0,
            'in'         => 39.3701,
            'ft'         => 3.28084,
            'yd'         => 1.09361,
            'mi'         => 1.09361,
            'm²'         => 1.0,
            'ft²'        => 10.76391,
            'L'          => 1.0,
            'm³'         => 0.001,
            'fl oz (US)' => 33.814,
            'qt (US)'    => 1.05669,
            'in³'        => 61.0237,
            'gal (US)'   => 0.26417217685798894,
            'ft³'        => 0.035314724827664144,
        ];

        foreach ($uomFactors as $name => $factor) {
            DB::table('unit_of_measures')
                ->where('name', $name)
                ->whereNull('deleted_at')
                ->update(['factor' => $factor]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_of_measures', function (Blueprint $table) {
            $table->decimal('factor', 15, 4)->nullable()->default(0)->change();
        });
    }
};
