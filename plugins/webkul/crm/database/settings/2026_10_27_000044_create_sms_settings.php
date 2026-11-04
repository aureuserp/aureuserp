<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crm_sms.is_active', false);
        $this->migrator->addEncrypted('crm_sms.auth_key', null);
        $this->migrator->add('crm_sms.sender_id', null);
        $this->migrator->add('crm_sms.route', '4');
    }

    public function down(): void
    {
        foreach (['is_active', 'auth_key', 'sender_id', 'route'] as $property) {
            $this->migrator->deleteIfExists('crm_sms.'.$property);
        }
    }
};
