<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crm_outgoing_mail.is_active', false);
        $this->migrator->add('crm_outgoing_mail.host', null);
        $this->migrator->add('crm_outgoing_mail.port', 587);
        $this->migrator->add('crm_outgoing_mail.encryption', 'tls');
        $this->migrator->add('crm_outgoing_mail.username', null);
        $this->migrator->addEncrypted('crm_outgoing_mail.password', null);
        $this->migrator->add('crm_outgoing_mail.from_address', null);
        $this->migrator->add('crm_outgoing_mail.from_name', null);
    }

    public function down(): void
    {
        foreach (['is_active', 'host', 'port', 'encryption', 'username', 'password', 'from_address', 'from_name'] as $property) {
            $this->migrator->deleteIfExists('crm_outgoing_mail.'.$property);
        }
    }
};
