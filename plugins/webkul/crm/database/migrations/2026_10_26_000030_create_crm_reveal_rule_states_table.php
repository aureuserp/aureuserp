<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_reveal_rule_states', function (Blueprint $table) {
            $table->foreignId('rule_id')->constrained('crm_reveal_rules')->cascadeOnDelete();
            $table->foreignId('state_id')->constrained('states')->cascadeOnDelete();

            $table->primary(['rule_id', 'state_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_reveal_rule_states');
    }
};
