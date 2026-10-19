<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_lost_reasons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->comment('Name');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_lost_reasons');
    }
};
