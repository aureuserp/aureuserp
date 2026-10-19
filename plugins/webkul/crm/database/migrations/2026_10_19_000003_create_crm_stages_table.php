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
        Schema::create('crm_stages', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->comment('Sort');
            $table->string('name')->comment('Name');
            $table->boolean('is_won')->default(false)->comment('Is Won Stage');
            $table->text('requirements')->nullable()->comment('Requirements');
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_stages');
    }
};
