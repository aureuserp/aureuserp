<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('website_contact.contact_form_enabled', true);
        $this->migrator->add('website_contact.contact_form_action', 'email');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('website_contact.contact_form_enabled');
        $this->migrator->deleteIfExists('website_contact.contact_form_action');
    }
};
