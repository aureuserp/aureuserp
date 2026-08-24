<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $columns = [
        'title',
        'content',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function up(): void
    {
        $locale = config('app.fallback_locale', 'en');

        DB::table('website_pages')
            ->orderBy('id')
            ->chunkById(100, function ($pages) use ($locale) {
                foreach ($pages as $page) {
                    $updates = [];

                    foreach ($this->columns as $column) {
                        $value = $page->{$column};

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
                        DB::table('website_pages')->where('id', $page->id)->update($updates);
                    }
                }
            });

        if (DB::getDriverName() === 'pgsql') {
            foreach ($this->columns as $column) {
                DB::statement("ALTER TABLE website_pages ALTER COLUMN {$column} TYPE jsonb USING {$column}::jsonb");
            }
        } else {
            Schema::table('website_pages', function (Blueprint $table) {
                $table->json('title')->change();
                $table->json('content')->change();
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
                DB::statement("ALTER TABLE website_pages ALTER COLUMN {$column} TYPE text USING {$column}::text");
            }
        } else {
            Schema::table('website_pages', function (Blueprint $table) {
                $table->text('title')->change();
                $table->text('content')->change();
                $table->text('meta_title')->nullable()->change();
                $table->text('meta_keywords')->nullable()->change();
                $table->text('meta_description')->nullable()->change();
            });
        }

        DB::table('website_pages')
            ->orderBy('id')
            ->chunkById(100, function ($pages) use ($locale) {
                foreach ($pages as $page) {
                    $updates = [];

                    foreach ($this->columns as $column) {
                        $decoded = json_decode($page->{$column} ?? '', true);

                        if (is_array($decoded)) {
                            $updates[$column] = $decoded[$locale] ?? (reset($decoded) ?: null);
                        }
                    }

                    if ($updates) {
                        DB::table('website_pages')->where('id', $page->id)->update($updates);
                    }
                }
            });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE website_pages ALTER COLUMN title TYPE varchar(255) USING title::varchar(255)');
        } else {
            Schema::table('website_pages', function (Blueprint $table) {
                $table->string('title')->change();
            });
        }
    }
};
