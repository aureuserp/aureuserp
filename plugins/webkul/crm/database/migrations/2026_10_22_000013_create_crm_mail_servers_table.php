<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Incoming mail servers (IMAP mailboxes) checked for emails sent to the sales team aliases.
     */
    public function up(): void
    {
        Schema::create('crm_mail_servers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('host');
            $table->unsignedSmallInteger('port')->default(993);
            $table->string('encryption')->default('ssl')->comment('ssl, tls or none');
            $table->boolean('validate_certificate')->default(true);
            $table->string('username');
            $table->text('password')->comment('Encrypted');
            $table->string('folder')->default('INBOX');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_fetched_at')->nullable();
            $table->text('last_error')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_mail_servers');
    }
};
