<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_off_leave_allocations', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('employee_company_id')->constrained('companies')->nullOnDelete();
        });

        DB::table('time_off_leave_allocations')->update([
            'company_id' => DB::raw('employee_company_id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('time_off_leave_allocations', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
