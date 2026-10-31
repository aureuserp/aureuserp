<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_visitors', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('website_id')->nullable()->comment('No websites table yet; always empty');
            $table->foreignId('partner_id')->nullable()->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('lang', 10)->nullable()->comment('Locale code, e.g. en');
            $table->string('access_token', 64)->unique()->comment('Visitor cookie value, or the partner id once logged in');
            $table->string('timezone')->nullable();
            $table->unsignedInteger('visit_count')->default(1);
            $table->timestamp('last_connection_datetime')->nullable()->index();

            $table->timestamps();
        });

        Schema::create('website_tracks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitor_id')->constrained('website_visitors')->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('website_pages')->nullOnDelete();
            $table->text('url');
            $table->timestamp('visit_datetime')->index();

            $table->index(['visitor_id', 'visit_datetime']);
        });

        Schema::table('website_pages', function (Blueprint $table) {
            $table->boolean('is_tracked')->default(true)->after('is_footer_visible')->comment('Record visitor page views');
        });
    }

    public function down(): void
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->dropColumn('is_tracked');
        });

        Schema::dropIfExists('website_tracks');
        Schema::dropIfExists('website_visitors');
    }
};
