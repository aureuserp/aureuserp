<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Partner (reseller) levels — Gold, Silver, Bronze by default — and each contact's CRM data:
     * its level and an optional level weight that overrides the level's.
     */
    public function up(): void
    {
        Schema::create('crm_partner_levels', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->unsignedInteger('sort')->default(0);
            $table->unsignedInteger('partner_weight')->default(1)->comment('Level weight: how likely partners of this level get leads; 0 = none');
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('crm_partners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('partner_id')->unique()->constrained('partners_partners')->cascadeOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('crm_partner_levels')->nullOnDelete();
            $table->unsignedInteger('level_weight')->nullable()->comment('Overrides the level weight when set');

            $table->timestamps();
        });

        $now = now();

        DB::table('crm_partner_levels')->insert([
            ['name' => 'Gold', 'sort' => 1, 'partner_weight' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Silver', 'sort' => 2, 'partner_weight' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bronze', 'sort' => 3, 'partner_weight' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_partners');
        Schema::dropIfExists('crm_partner_levels');
    }
};
