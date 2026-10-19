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
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('partner_id')->comment('Company Name');
            $table->string('contact_name')->nullable()->after('company_name')->comment('Contact Name');
            $table->string('job_title')->nullable()->after('contact_name')->comment('Job Position');
            $table->string('website')->nullable()->after('phone')->comment('Website');
            $table->string('street1')->nullable()->after('website')->comment('Street');
            $table->string('street2')->nullable()->after('street1')->comment('Street 2');
            $table->string('city')->nullable()->after('street2')->comment('City');
            $table->string('zip')->nullable()->after('city')->comment('ZIP');
            $table->foreignId('state_id')->nullable()->after('zip')->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->after('state_id')->constrained('countries')->nullOnDelete();
            $table->foreignId('utm_campaign_id')->nullable()->after('lost_feedback')->constrained('utm_campaigns')->nullOnDelete();
            $table->foreignId('utm_medium_id')->nullable()->after('utm_campaign_id')->constrained('utm_mediums')->nullOnDelete();
            $table->foreignId('utm_source_id')->nullable()->after('utm_medium_id')->constrained('utm_sources')->nullOnDelete();
            $table->string('referred_by')->nullable()->after('utm_source_id')->comment('Referred By');
            $table->dateTime('assigned_at')->nullable()->after('expected_closing_date')->comment('Assignment Date');
            $table->dateTime('converted_at')->nullable()->after('closed_at')->comment('Converted To Opportunity At');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('state_id');
            $table->dropConstrainedForeignId('country_id');
            $table->dropConstrainedForeignId('utm_campaign_id');
            $table->dropConstrainedForeignId('utm_medium_id');
            $table->dropConstrainedForeignId('utm_source_id');
            $table->dropColumn([
                'company_name',
                'contact_name',
                'job_title',
                'website',
                'street1',
                'street2',
                'city',
                'zip',
                'referred_by',
                'assigned_at',
                'converted_at',
            ]);
        });
    }
};
