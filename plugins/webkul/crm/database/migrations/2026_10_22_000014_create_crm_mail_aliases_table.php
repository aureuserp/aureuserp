<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The email alias of a sales team (e.g. sales@yourcompany.com): emails sent to it create
     * leads in the team. `alias_contact` says who may send to it.
     */
    public function up(): void
    {
        Schema::create('crm_mail_aliases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->unique()->constrained('sales_teams')->cascadeOnDelete();
            $table->string('alias_name')->nullable()->comment('Part before the @');
            $table->string('alias_domain')->nullable()->comment('Part after the @; empty matches any domain');
            $table->string('alias_contact')->default('everyone')->comment('everyone, partners, followers or employees');

            $table->timestamps();

            $table->unique(['alias_name', 'alias_domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_mail_aliases');
    }
};
