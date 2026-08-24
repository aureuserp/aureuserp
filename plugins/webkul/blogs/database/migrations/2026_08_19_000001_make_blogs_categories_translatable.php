<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $columns = [
        'name',
        'sub_title',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function up(): void
    {
        $locale = config('app.fallback_locale', 'en');

        DB::table('blogs_categories')
            ->orderBy('id')
            ->chunkById(100, function ($categories) use ($locale) {
                foreach ($categories as $category) {
                    $updates = [];

                    foreach ($this->columns as $column) {
                        $value = $category->{$column};

                        if ($value === null) {
                            continue;
                        }

                        $decoded = json_decode($value, true);

                        if (is_array($decoded)) {
                            continue;
                        }

                        $updates[$column] = json_encode([$locale => $value], JSON_UNESCAPED_UNICODE);
                    }

                    if ($updates) {
                        DB::table('blogs_categories')->where('id', $category->id)->update($updates);
                    }
                }
            });

        if (DB::getDriverName() === 'pgsql') {
            foreach ($this->columns as $column) {
                DB::statement("ALTER TABLE blogs_categories ALTER COLUMN {$column} TYPE jsonb USING {$column}::jsonb");
            }
        } else {
            Schema::table('blogs_categories', function (Blueprint $table) {
                $table->json('name')->change();
                $table->json('sub_title')->nullable()->change();
                $table->json('meta_title')->nullable()->change();
                $table->json('meta_keywords')->nullable()->change();
                $table->json('meta_description')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $locale = config('app.fallback_locale', 'en');

        if (DB::getDriverName() === 'pgsql') {
            foreach ($this->columns as $column) {
                DB::statement("ALTER TABLE blogs_categories ALTER COLUMN {$column} TYPE text USING {$column}::text");
            }
        } else {
            Schema::table('blogs_categories', function (Blueprint $table) {
                $table->text('name')->change();
                $table->text('sub_title')->nullable()->change();
                $table->text('meta_title')->nullable()->change();
                $table->text('meta_keywords')->nullable()->change();
                $table->text('meta_description')->nullable()->change();
            });
        }

        DB::table('blogs_categories')
            ->orderBy('id')
            ->chunkById(100, function ($categories) use ($locale) {
                foreach ($categories as $category) {
                    $updates = [];

                    foreach ($this->columns as $column) {
                        $decoded = json_decode($category->{$column} ?? '', true);

                        if (is_array($decoded)) {
                            $updates[$column] = $decoded[$locale] ?? (reset($decoded) ?: null);
                        }
                    }

                    if ($updates) {
                        DB::table('blogs_categories')->where('id', $category->id)->update($updates);
                    }
                }
            });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE blogs_categories ALTER COLUMN name TYPE varchar(255) USING name::varchar(255)');
            DB::statement('ALTER TABLE blogs_categories ALTER COLUMN meta_title TYPE varchar(255) USING meta_title::varchar(255)');
            DB::statement('ALTER TABLE blogs_categories ALTER COLUMN meta_keywords TYPE varchar(255) USING meta_keywords::varchar(255)');
        } else {
            Schema::table('blogs_categories', function (Blueprint $table) {
                $table->string('name')->change();
                $table->string('meta_title')->nullable()->change();
                $table->string('meta_keywords')->nullable()->change();
            });
        }
    }
};
