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
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('scope_type')->nullable();
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('variant')->default('');
            $table->string('name')->nullable();
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->unsignedTinyInteger('padding')->default(5);
            $table->unsignedBigInteger('next_number')->default(1);
            $table->unsignedInteger('step')->default(1);
            $table->string('reset_frequency')->default('never');
            $table->string('period_key')->nullable();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->restrictOnDelete();

            $table->unsignedBigInteger('company_scope')->storedAs('coalesce(company_id, 0)');

            $table->timestamps();

            $table->unique(['code', 'company_scope']);
            $table->unique(['scope_type', 'scope_id', 'variant', 'company_scope'], 'sequences_scope_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};
