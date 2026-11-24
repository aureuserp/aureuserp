<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees_employees', function (Blueprint $table) {
            $table->string('employee_type')->nullable()->default(null)->change();
            $table->date('joining_date')->nullable()->after('employee_type');
            $table->decimal('salary', 15, 4)->nullable()->after('joining_date');
            $table->foreignId('salary_currency_id')->nullable()->after('salary')->constrained('currencies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees_employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('salary_currency_id');
            $table->dropColumn(['joining_date', 'salary']);
        });
    }
};
