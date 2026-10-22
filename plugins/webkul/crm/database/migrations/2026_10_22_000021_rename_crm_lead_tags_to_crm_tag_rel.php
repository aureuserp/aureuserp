<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The lead ↔ tag relation table is named `crm_tag_rel` (lead_id, tag_id). Existing links
     * are kept.
     */
    public function up(): void
    {
        if (Schema::hasTable('crm_lead_tags') && ! Schema::hasTable('crm_tag_rel')) {
            Schema::rename('crm_lead_tags', 'crm_tag_rel');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('crm_tag_rel') && ! Schema::hasTable('crm_lead_tags')) {
            Schema::rename('crm_tag_rel', 'crm_lead_tags');
        }
    }
};
