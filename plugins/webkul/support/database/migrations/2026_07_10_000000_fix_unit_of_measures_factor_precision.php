<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $factors = [
            'Dozens'   => '0.08333333333333333',
            'gal (US)' => '0.26417217685798894',
            'ft³'      => '0.035314724827664144',
        ];

        foreach ($factors as $name => $factor) {
            DB::table('unit_of_measures')
                ->where('name', $name)
                ->update(['factor' => $factor]);
        }
    }

    /**
     * Data-only fix; nothing to reverse.
     */
    public function down(): void
    {
        //
    }
};
