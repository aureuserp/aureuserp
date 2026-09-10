<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $sharedTypeNames = [
        'Paid Time Off',
        'Sick Time Off',
        'Compensatory Days',
        'Compensatory Days test',
        'Unpaid',
        'Extra Hours',
    ];

    public function up(): void
    {
        DB::table('time_off_leave_types')
            ->whereIn('name', $this->sharedTypeNames)
            ->update(['company_id' => null]);
    }

    public function down(): void
    {
        $companyId = DB::table('companies')->min('id');

        DB::table('time_off_leave_types')
            ->whereIn('name', $this->sharedTypeNames)
            ->whereNull('company_id')
            ->update(['company_id' => $companyId]);
    }
};
