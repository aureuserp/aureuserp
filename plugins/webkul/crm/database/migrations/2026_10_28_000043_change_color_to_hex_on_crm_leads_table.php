<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $palette = [
        1  => '#F06050',
        2  => '#F4A460',
        3  => '#F7CD1F',
        4  => '#6CC1ED',
        5  => '#814968',
        6  => '#EB7E7F',
        7  => '#2C8397',
        8  => '#475577',
        9  => '#D6145F',
        10 => '#30C381',
        11 => '#9365B8',
    ];

    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->default(null)->comment('Kanban card colour (hex)')->change();
        });

        DB::table('crm_leads')->whereIn('color', ['0', ''])->update(['color' => null]);

        foreach ($this->palette as $index => $hex) {
            DB::table('crm_leads')->where('color', (string) $index)->update(['color' => $hex]);
        }
    }

    public function down(): void
    {
        foreach ($this->palette as $index => $hex) {
            DB::table('crm_leads')->where('color', $hex)->update(['color' => (string) $index]);
        }

        DB::table('crm_leads')->whereNull('color')->orWhere('color', 'like', '#%')->update(['color' => '0']);

        Schema::table('crm_leads', function (Blueprint $table) {
            $table->unsignedTinyInteger('color')->default(0)->comment('Kanban card colour: 0 = none, 1-11 = palette')->change();
        });
    }
};
