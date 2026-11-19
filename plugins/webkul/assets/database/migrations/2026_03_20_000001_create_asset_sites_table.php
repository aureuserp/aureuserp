<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_warehouses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('code')->nullable();

            $table->foreignId('custodian_employee_id')
                ->nullable()
                ->constrained('employees_employees')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'is_active']);
        });

        Schema::create('asset_site_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('icon')->nullable()->default('heroicon-m-map-pin');
            $table->string('color')->nullable()->default('#3b82f6');

            $table->timestamps();

            $table->unique(['company_id', 'name']);
        });

        $now = now();

        DB::table('asset_site_types')->insert([
            [
                'company_id' => null,
                'name'       => 'Office',
                'icon'       => 'heroicon-m-building-office',
                'color'      => '#3b82f6',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => null,
                'name'       => 'Server Room',
                'icon'       => 'heroicon-m-server-stack',
                'color'      => '#6366f1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => null,
                'name'       => 'Workshop',
                'icon'       => 'heroicon-m-wrench-screwdriver',
                'color'      => '#f59e0b',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => null,
                'name'       => 'Field',
                'icon'       => 'heroicon-m-map',
                'color'      => '#10b981',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => null,
                'name'       => 'Vehicle',
                'icon'       => 'heroicon-m-truck',
                'color'      => '#06b6d4',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        Schema::create('asset_sites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('asset_sites')
                ->restrictOnDelete();

            $table->foreignId('site_type_id')
                ->constrained('asset_site_types')
                ->restrictOnDelete();

            $table->string('usage_type')->default('asset_location')->index();
            $table->string('name')->index();
            $table->string('full_name')->nullable()->index();
            $table->string('parent_path')->nullable()->default('/');
            $table->string('code')->nullable();

            $table->foreignId('default_custodian_employee_id')
                ->nullable()
                ->constrained('employees_employees')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'parent_id']);
            $table->index(['company_id', 'site_type_id']);
            $table->index(['company_id', 'usage_type', 'is_active']);
        });

        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('asset_categories')
                ->restrictOnDelete();

            $table->string('name')->index();
            $table->string('full_name')->nullable()->index();
            $table->string('parent_path')->nullable()->default('/');

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['company_id', 'parent_id', 'name']);
        });

        $computersId = DB::table('asset_categories')->insertGetId([
            'company_id'  => null,
            'parent_id'   => null,
            'name'        => 'Computers & IT',
            'full_name'   => 'Computers & IT',
            'parent_path' => '/',
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        DB::table('asset_categories')->insert([
            [
                'company_id'  => null,
                'parent_id'   => $computersId,
                'name'        => 'Laptops',
                'full_name'   => 'Computers & IT / Laptops',
                'parent_path' => '/'.$computersId.'/',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'company_id'  => null,
                'parent_id'   => $computersId,
                'name'        => 'Tablets',
                'full_name'   => 'Computers & IT / Tablets',
                'parent_path' => '/'.$computersId.'/',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'company_id'  => null,
                'parent_id'   => null,
                'name'        => 'Mobile & Communication',
                'full_name'   => 'Mobile & Communication',
                'parent_path' => '/',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'company_id'  => null,
                'parent_id'   => null,
                'name'        => 'Vehicles',
                'full_name'   => 'Vehicles',
                'parent_path' => '/',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'company_id'  => null,
                'parent_id'   => null,
                'name'        => 'Office Furniture',
                'full_name'   => 'Office Furniture',
                'parent_path' => '/',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'company_id'  => null,
                'parent_id'   => null,
                'name'        => 'Machinery & Equipment',
                'full_name'   => 'Machinery & Equipment',
                'parent_path' => '/',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        Schema::create('asset_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('asset_categories')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('model')->nullable();
            $table->decimal('cost', 15, 4)->nullable()->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'category_id']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_products');
        Schema::dropIfExists('asset_categories');
        Schema::dropIfExists('asset_sites');
        Schema::dropIfExists('asset_site_types');
        Schema::dropIfExists('asset_warehouses');
    }
};
