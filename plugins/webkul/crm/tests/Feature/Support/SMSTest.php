<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Filament\Clusters\Settings\Pages\ManageSMS;
use Webkul\Crm\Services\SMS\SMSSender;
use Webkul\Crm\Settings\SMSSettings;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Models\Country;

it('lists the SMS gateway page in the global settings', function () {
    expect(ManageSMS::getCluster())->toBe(Settings::class);
});

it('saves the SMS gateway and keeps the auth key when left empty', function () {
    CRMHelper::actingAs(['page_crm_manage_s_m_s']);

    Livewire::test(ManageSMS::class)
        ->fillForm(['is_active' => true, 'auth_key' => 'secret-key', 'sender_id' => 'AUREUS', 'route' => '4'])
        ->call('save')
        ->assertHasNoFormErrors();

    Livewire::test(ManageSMS::class)
        ->fillForm(['sender_id' => 'ACMEIN', 'auth_key' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(SMSSettings::class)->refresh();

    expect($settings->sender_id)->toBe('ACMEIN')
        ->and($settings->auth_key)->toBe('secret-key')
        ->and(app(SMSSender::class)->usesCustomGateway())->toBeTrue();
});

it('is forbidden without its page permission', function () {
    CRMHelper::actingAs([]);

    Livewire::test(ManageSMS::class)->assertForbidden();
});

it('normalizes numbers to international format', function (?string $number, ?string $phoneCode, ?string $expected) {
    expect(SMSSender::normalize($number, $phoneCode))->toBe($expected);
})->with([
    'plus prefix'       => ['+91 98765 43210', null, '919876543210'],
    'double zero'       => ['0091 98765 43210', null, '919876543210'],
    'country code'      => ['098765 43210', '+91', '919876543210'],
    'no country'        => ['07068703646', null, null],
    'too short'         => ['+91 123', null, null],
    'empty'             => [null, '91', null],
]);

it('counts characters and SMS parts per encoding', function (string $message, array $expected) {
    expect(SMSSender::segments($message))->toBe($expected);
})->with([
    'empty'       => ['', ['characters' => 0, 'parts' => 0, 'encoding' => 'GSM7']],
    'single gsm'  => ['Hello', ['characters' => 5, 'parts' => 1, 'encoding' => 'GSM7']],
    'extended'    => ['{x}', ['characters' => 5, 'parts' => 1, 'encoding' => 'GSM7']],
    'two parts'   => [str_repeat('a', 161), ['characters' => 161, 'parts' => 2, 'encoding' => 'GSM7']],
    'unicode'     => ['नमस्ते', ['characters' => 6, 'parts' => 1, 'encoding' => 'UCS2']],
]);

it('sends an SMS from the lead page and logs it on the lead', function () {
    Http::fake(['api.msg91.com/*' => Http::response('ok')]);

    CRMHelper::configureSMSGateway();
    CRMHelper::actingAsCRMUser(['lead']);

    $country = Country::query()->whereNotNull('phone_code')->firstOrFail();
    $lead = CRMHelper::lead(['phone' => '098765 43210', 'country_id' => $country->id]);

    Livewire::test(ViewLead::class, ['record' => $lead->getKey()])
        ->callAction('sendSMS', ['recipient' => '098765 43210', 'message' => 'Your quote is ready'])
        ->assertHasNoActionErrors();

    $number = preg_replace('/\D+/', '', $country->phone_code).'9876543210';

    Http::assertSent(fn (Request $request): bool => $request['mobiles'] === $number
        && $request['message'] === 'Your quote is ready'
        && $request['authkey'] === 'test-key');

    expect($lead->messages()->where('type', 'comment')->where('body', 'Your quote is ready')->exists())->toBeTrue();
});

it('rejects a number without a country code when the lead has no country', function () {
    Http::fake();

    CRMHelper::configureSMSGateway();
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead(['phone' => '07068703646', 'country_id' => null, 'partner_id' => null]);

    Livewire::test(ViewLead::class, ['record' => $lead->getKey()])
        ->callAction('sendSMS', ['recipient' => '07068703646', 'message' => 'Hi'])
        ->assertHasActionErrors(['recipient']);

    Http::assertNothingSent();
});
