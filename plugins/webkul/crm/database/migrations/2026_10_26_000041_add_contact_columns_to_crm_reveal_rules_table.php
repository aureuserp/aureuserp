<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_reveal_rules', function (Blueprint $table) {
            $table->foreignId('preferred_role_id')->nullable()->after('contact_filter_type')->constrained('crm_lead_roles')->nullOnDelete();
            $table->foreignId('seniority_id')->nullable()->after('preferred_role_id')->constrained('crm_lead_seniorities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_reveal_rules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seniority_id');
            $table->dropConstrainedForeignId('preferred_role_id');
        });
    }
};
