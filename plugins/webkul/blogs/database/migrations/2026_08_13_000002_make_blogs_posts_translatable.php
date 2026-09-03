<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $columns = [
        'title',
        'sub_title',
        'content',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function up(): void
    {
        $locale = config('app.fallback_locale', 'en');

        DB::table('blogs_posts')
            ->orderBy('id')
            ->chunkById(100, function ($posts) use ($locale) {
                foreach ($posts as $post) {
                    $updates = [];

                    foreach ($this->columns as $column) {
                        $value = $post->{$column};

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
                        DB::table('blogs_posts')->where('id', $post->id)->update($updates);
                    }
                }
            });

        if (DB::getDriverName() === 'pgsql') {
            foreach ($this->columns as $column) {
                DB::statement("ALTER TABLE blogs_posts ALTER COLUMN {$column} TYPE jsonb USING {$column}::jsonb");
            }
        } else {
            Schema::table('blogs_posts', function (Blueprint $table) {
                $table->json('title')->change();
                $table->json('sub_title')->nullable()->change();
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
                DB::statement("ALTER TABLE blogs_posts ALTER COLUMN {$column} TYPE text USING {$column}::text");
            }
        } else {
            Schema::table('blogs_posts', function (Blueprint $table) {
                $table->text('title')->change();
                $table->text('sub_title')->nullable()->change();
                $table->text('content')->change();
                $table->text('meta_title')->nullable()->change();
                $table->text('meta_keywords')->nullable()->change();
                $table->text('meta_description')->nullable()->change();
            });
        }

        DB::table('blogs_posts')
            ->orderBy('id')
            ->chunkById(100, function ($posts) use ($locale) {
                foreach ($posts as $post) {
                    $updates = [];

                    foreach ($this->columns as $column) {
                        $decoded = json_decode($post->{$column} ?? '', true);

                        if (is_array($decoded)) {
                            $updates[$column] = $decoded[$locale] ?? (reset($decoded) ?: null);
                        }
                    }

                    if ($updates) {
                        DB::table('blogs_posts')->where('id', $post->id)->update($updates);
                    }
                }
            });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE blogs_posts ALTER COLUMN title TYPE varchar(255) USING title::varchar(255)');
        } else {
            Schema::table('blogs_posts', function (Blueprint $table) {
                $table->string('title')->change();
            });
        }
    }
};
