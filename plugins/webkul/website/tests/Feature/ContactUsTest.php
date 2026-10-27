<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Website\Filament\Customer\Pages\ContactUs;
use Webkul\Website\Mail\ContactFormMail;
use Webkul\Website\Services\ContactFormHandler;
use Webkul\Website\Settings\ContactSettings;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('website');

    Filament::setCurrentPanel(Filament::getPanel('customer'));

    RateLimiter::clear('website-contact-us:127.0.0.1');

    contactSettings(enabled: true, action: ContactFormHandler::ACTION_EMAIL);
});

function contactSettings(bool $enabled, string $action): void
{
    $settings = app(ContactSettings::class);
    $settings->contact_form_enabled = $enabled;
    $settings->contact_form_action = $action;
    $settings->save();
}

function contactFormData(array $overrides = []): array
{
    return [
        'name'     => 'Jane Visitor',
        'phone'    => '+91 98765 43210',
        'email'    => 'jane@example.com',
        'company'  => 'Acme Ltd',
        'subject'  => 'Need a quote',
        'question' => "Line one\nLine two",
        ...$overrides,
    ];
}

it('emails the contact form to the configured mail address', function () {
    Mail::fake();
    config(['mail.from.address' => 'sales@example.com']);

    Livewire::test(ContactUs::class)
        ->fillForm(contactFormData())
        ->call('submit')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $companyEmail = Company::query()->where('is_active', true)->orderBy('id')->value('email');

    Mail::assertSent(ContactFormMail::class, fn (ContactFormMail $mail): bool => $mail->hasTo('sales@example.com')
        && $mail->hasFrom('jane@example.com', 'Jane Visitor')
        && $mail->hasReplyTo('jane@example.com')
        && (blank($companyEmail) || $mail->hasCc($companyEmail)));
});

it('requires every field except company', function () {
    Mail::fake();

    Livewire::test(ContactUs::class)
        ->fillForm(contactFormData(['name' => '', 'phone' => '', 'email' => 'not-an-email', 'company' => '', 'subject' => '', 'question' => '']))
        ->call('submit')
        ->assertHasFormErrors(['name' => 'required', 'phone' => 'required', 'email' => 'email', 'subject' => 'required', 'question' => 'required'])
        ->assertHasNoFormErrors(['company']);

    Mail::assertNothingSent();
});

it('silently drops submissions that fill the honeypot field', function () {
    Mail::fake();

    Livewire::test(ContactUs::class)
        ->fillForm(contactFormData([ContactUs::HONEYPOT => 'http://spam.test']))
        ->call('submit')
        ->assertNotified();

    Mail::assertNothingSent();
});

it('limits submissions per IP address', function () {
    Mail::fake();

    foreach (range(1, ContactUs::MAX_ATTEMPTS + 1) as $attempt) {
        Livewire::test(ContactUs::class)
            ->fillForm(contactFormData())
            ->call('submit');
    }

    Mail::assertSentCount(ContactUs::MAX_ATTEMPTS);
});

it('is not reachable when the contact form is disabled', function () {
    contactSettings(enabled: false, action: ContactFormHandler::ACTION_EMAIL);

    expect(ContactUs::canAccess())->toBeFalse();

    Livewire::test(ContactUs::class)->assertNotFound();
});

it('falls back to email when the saved action is not available', function () {
    contactSettings(enabled: true, action: 'unknown');

    expect(ContactFormHandler::action())->toBe(ContactFormHandler::ACTION_EMAIL);
});

it('offers lead and opportunity actions only when the CRM plugin is installed', function () {
    $options = array_keys(ContactFormHandler::actionOptions());

    expect($options)->toBe(ContactFormHandler::crmAvailable()
        ? [ContactFormHandler::ACTION_EMAIL, ContactFormHandler::ACTION_LEAD, ContactFormHandler::ACTION_OPPORTUNITY]
        : [ContactFormHandler::ACTION_EMAIL]);
});

it('creates a CRM record of the configured type', function (string $action) {
    TestBootstrapHelper::ensurePluginInstalled('crm');

    contactSettings(enabled: true, action: $action);

    Livewire::test(ContactUs::class)
        ->fillForm(contactFormData())
        ->call('submit')
        ->assertHasNoFormErrors();

    $lead = Lead::query()->withoutGlobalScopes()->latest('id')->first();

    expect($lead->type)->toBe($action)
        ->and($lead->name)->toBe('Need a quote')
        ->and($lead->contact_name)->toBe('Jane Visitor')
        ->and($lead->email)->toBe('jane@example.com')
        ->and($lead->phone)->toBe('+91 98765 43210')
        ->and($lead->company_name)->toBe('Acme Ltd')
        ->and($lead->description)->toContain('Line one<br />');
})->with([ContactFormHandler::ACTION_LEAD, ContactFormHandler::ACTION_OPPORTUNITY]);

it('does not assign a website lead to the logged-in admin user', function () {
    TestBootstrapHelper::ensurePluginInstalled('crm');

    contactSettings(enabled: true, action: ContactFormHandler::ACTION_LEAD);

    $this->actingAs(User::factory()->create());

    Livewire::test(ContactUs::class)
        ->fillForm(contactFormData())
        ->call('submit')
        ->assertHasNoFormErrors();

    $lead = Lead::query()->withoutGlobalScopes()->latest('id')->first();

    expect($lead->creator_id)->toBeNull()
        ->and($lead->type)->toBe(ContactFormHandler::ACTION_LEAD);
});

it('rejects HTML, scripts and invalid phone numbers', function () {
    Mail::fake();

    Livewire::test(ContactUs::class)
        ->fillForm(contactFormData([
            'name'     => '<script>alert(1)</script>',
            'phone'    => 'call me',
            'company'  => '<img src=x onerror=alert(1)>',
            'subject'  => 'javascript:alert(1)',
            'question' => '<a href="#" onclick="evil()">click</a>',
        ]))
        ->call('submit')
        ->assertHasFormErrors(['name' => 'not_regex', 'phone' => 'regex', 'company' => 'not_regex', 'subject' => 'not_regex', 'question' => 'not_regex']);

    Mail::assertNothingSent();
});

it('allows plain text that only looks like comparisons', function () {
    Mail::fake();

    Livewire::test(ContactUs::class)
        ->fillForm(contactFormData(['question' => 'Is 2 < 3 and 5 > 4?']))
        ->call('submit')
        ->assertHasNoFormErrors();

    Mail::assertSentCount(1);
});

it('strips markup, control characters and line breaks before saving or mailing', function () {
    $clean = ContactFormHandler::sanitize(contactFormData([
        'name'     => "Jane<script>x</script>\r\nBcc: spy@example.com",
        'phone'    => "+91 98765\x00 43210",
        'company'  => '&lt;b&gt;Acme&lt;/b&gt;',
        'subject'  => "Need\u{202E} a quote",
        'question' => "Hello\r\n\r\n\r\n\r\n<b>world</b>",
    ]));

    expect($clean['name'])->toBe('Janex Bcc: spy@example.com')
        ->and($clean['phone'])->toBe('+91 98765 43210')
        ->and($clean['company'])->toBe('Acme')
        ->and($clean['subject'])->toBe('Need a quote')
        ->and($clean['question'])->toBe("Hello\n\nworld");
});

it('refuses a submission that is empty after sanitizing', function () {
    ContactFormHandler::sanitize(contactFormData(['subject' => '<p></p>']));
})->throws(InvalidArgumentException::class);

it('stores SQL injection attempts as plain text', function () {
    TestBootstrapHelper::ensurePluginInstalled('crm');

    contactSettings(enabled: true, action: ContactFormHandler::ACTION_LEAD);

    $leadCount = Lead::query()->withoutGlobalScopes()->withTrashed()->count();

    Livewire::test(ContactUs::class)
        ->fillForm(contactFormData([
            'name'    => "Robert'); DROP TABLE crm_leads;--",
            'company' => "' OR '1'='1",
            'subject' => "1' UNION SELECT password FROM users--",
        ]))
        ->call('submit')
        ->assertHasNoFormErrors();

    $lead = Lead::query()->withoutGlobalScopes()->latest('id')->first();

    expect(Lead::query()->withoutGlobalScopes()->withTrashed()->count())->toBe($leadCount + 1)
        ->and($lead->contact_name)->toBe("Robert'); DROP TABLE crm_leads;--")
        ->and($lead->company_name)->toBe("' OR '1'='1")
        ->and($lead->name)->toBe("1' UNION SELECT password FROM users--");
});

it('hides the lead action and creates opportunities when CRM leads are turned off', function () {
    TestBootstrapHelper::ensurePluginInstalled('crm');

    contactSettings(enabled: true, action: ContactFormHandler::ACTION_LEAD);

    $crmSettings = app(CRMSettings::class);
    $crmSettings->use_leads = false;
    $crmSettings->save();

    expect(array_keys(ContactFormHandler::actionOptions()))->toBe([ContactFormHandler::ACTION_EMAIL, ContactFormHandler::ACTION_OPPORTUNITY])
        ->and(ContactFormHandler::action())->toBe(ContactFormHandler::ACTION_OPPORTUNITY);

    $crmSettings->use_leads = true;
    $crmSettings->save();

    expect(array_keys(ContactFormHandler::actionOptions()))->toContain(ContactFormHandler::ACTION_LEAD)
        ->and(ContactFormHandler::action())->toBe(ContactFormHandler::ACTION_LEAD);
});
