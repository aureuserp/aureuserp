<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The opportunity a quotation was created from (CRM → opportunity → New Quotation).
     */
    public function up(): void
    {
        if (Schema::hasColumn('sales_orders', 'opportunity_id')) {
            return;
        }

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->foreignId('opportunity_id')->nullable()->after('team_id')->comment('CRM opportunity')->constrained('crm_leads')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('sales_orders', 'opportunity_id')) {
            return;
        }

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opportunity_id');
        });
    }
};
