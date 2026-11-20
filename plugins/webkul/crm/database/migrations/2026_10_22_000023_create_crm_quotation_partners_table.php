<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each "New Quotation" started from an opportunity: the customer choice made (create a
     * customer, link an existing one, none, or the opportunity's existing customer) and the
     * quotation that was then saved.
     */
    public function up(): void
    {
        Schema::create('crm_quotation_partners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('opportunity_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->string('action')->comment('create, link, none or existing');
            $table->foreignId('partner_id')->nullable()->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('quotation_id')->nullable()->comment('Filled when the quotation is saved')->constrained('sales_orders')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_quotation_partners');
    }
};
