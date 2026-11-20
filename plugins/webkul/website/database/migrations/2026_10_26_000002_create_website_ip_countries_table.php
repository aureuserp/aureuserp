<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_ip_countries', function (Blueprint $table) {
            $table->binary('ip_from', 16, fixed: true)->comment('First address of the range, 16 bytes (IPv4 mapped to IPv6)');
            $table->binary('ip_to', 16, fixed: true)->comment('Last address of the range, 16 bytes');
            $table->char('country_code', 2);

            $table->primary('ip_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_ip_countries');
    }
};
