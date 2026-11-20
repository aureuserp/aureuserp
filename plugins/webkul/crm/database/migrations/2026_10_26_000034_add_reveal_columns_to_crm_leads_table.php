<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->string('reveal_ip', 45)->nullable()->after('referred_by')->comment('IP address of the website visitor the lead was generated from');
            $table->foreignId('reveal_rule_id')->nullable()->after('reveal_ip')->constrained('crm_reveal_rules')->nullOnDelete();

            $table->index('reveal_ip');
        });
    }

    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reveal_rule_id');
            $table->dropIndex(['reveal_ip']);
            $table->dropColumn('reveal_ip');
        });
    }
};
