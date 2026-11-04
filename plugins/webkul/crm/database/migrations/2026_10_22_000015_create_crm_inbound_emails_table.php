<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every email read from an incoming mail server and what was done with it. The message id
     * stops an email being imported twice and lets replies find the lead they belong to.
     */
    public function up(): void
    {
        Schema::create('crm_inbound_emails', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mail_server_id')->nullable()->constrained('crm_mail_servers')->nullOnDelete();
            $table->foreignId('alias_id')->nullable()->constrained('crm_mail_aliases')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->string('message_id')->unique();
            $table->string('in_reply_to')->nullable();
            $table->string('from_email')->nullable()->index();
            $table->string('from_name')->nullable();
            $table->text('recipients')->nullable();
            $table->string('subject')->nullable();
            $table->string('status')->index()->comment('lead_created, reply_added, rejected, ignored or failed');
            $table->text('reason')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_inbound_emails');
    }
};
