<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Recurring plans (Monthly, Yearly…) and the recurring revenue of an opportunity: an amount
     * and the plan it is billed on.
     */
    public function up(): void
    {
        Schema::create('crm_recurring_plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->unsignedInteger('number_of_months')->default(1)->comment('Months the recurring revenue covers');
            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('crm_leads', function (Blueprint $table) {
            $table->decimal('recurring_revenue', 15, 4)->default(0)->after('expected_revenue');
            $table->foreignId('recurring_plan_id')->nullable()->after('recurring_revenue')->constrained('crm_recurring_plans')->nullOnDelete();
        });

        $now = now();

        DB::table('crm_recurring_plans')->insert([
            ['name' => 'Monthly', 'number_of_months' => 1, 'sort' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Yearly', 'number_of_months' => 12, 'sort' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Over 3 years', 'number_of_months' => 36, 'sort' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Over 5 years', 'number_of_months' => 60, 'sort' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recurring_plan_id');
            $table->dropColumn('recurring_revenue');
        });

        Schema::dropIfExists('crm_recurring_plans');
    }
};
