<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Inventory\Enums\MoveType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories_operations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('origin')->nullable();
            $table->string('move_type')->default(MoveType::DIRECT);
            $table->string('state')->nullable();
            $table->boolean('is_favorite')->default(0);
            $table->boolean('has_deadline_issue')->default(0);
            $table->boolean('is_printed')->default(0);
            $table->boolean('is_locked')->default(0);
            $table->datetime('deadline')->nullable();
            $table->datetime('scheduled_at')->nullable();
            $table->datetime('closed_at')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('operation_type_id')
                ->constrained('inventories_operation_types')
                ->restrictOnDelete();

            $table->foreignId('source_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('destination_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('back_order_id')
                ->nullable()
                ->constrained('inventories_operations')
                ->nullOnDelete();

            $table->foreignId('return_id')
                ->nullable()
                ->constrained('inventories_operations')
                ->nullOnDelete();

            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories_operations');
    }
};
