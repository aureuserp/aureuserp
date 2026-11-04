<?php

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\InboundEmailResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource;
use Webkul\Crm\Filament\Clusters\Settings\Pages\ManageOutgoingMail;
use Webkul\Crm\Mail\LeadMessageMail;
use Webkul\Crm\Services\Mail\OutgoingMailer;
use Webkul\Crm\Settings\OutgoingMailSettings;
use Webkul\Support\Filament\Clusters\Settings;

it('lists the email pages in the global settings', function () {
    expect(MailServerResource::getCluster())->toBe(Settings::class)
        ->and(InboundEmailResource::getCluster())->toBe(Settings::class)
        ->and(ManageOutgoingMail::getCluster())->toBe(Settings::class);
});

it('saves the outgoing mail server and keeps the password when left empty', function () {
    CRMHelper::actingAs(['page_crm_manage_outgoing_mail']);

    Livewire::test(ManageOutgoingMail::class)
        ->fillForm([
            'is_active'    => true,
            'host'         => 'smtp.example.test',
            'encryption'   => 'tls',
            'port'         => 587,
            'username'     => 'mailer',
            'password'     => 'secret',
            'from_address' => 'crm@example.test',
            'from_name'    => 'CRM',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    Livewire::test(ManageOutgoingMail::class)
        ->fillForm(['host' => 'smtp2.example.test', 'password' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(OutgoingMailSettings::class)->refresh();

    expect($settings->host)->toBe('smtp2.example.test')
        ->and($settings->password)->toBe('secret')
        ->and(app(OutgoingMailer::class)->usesCustomServer())->toBeTrue();
});

it('is forbidden without its page permission', function () {
    CRMHelper::actingAs([]);

    Livewire::test(ManageOutgoingMail::class)->assertForbidden();
});

it('sends through the application mailer while the custom server is off', function () {
    Mail::fake();

    $settings = app(OutgoingMailSettings::class);
    $settings->is_active = false;
    $settings->save();

    app(OutgoingMailer::class)->send('someone@example.test', 'Someone', new LeadMessageMail('Hello', '<p>Hi</p>'));

    Mail::assertSent(LeadMessageMail::class, fn (LeadMessageMail $mail): bool => $mail->hasTo('someone@example.test'));
});
