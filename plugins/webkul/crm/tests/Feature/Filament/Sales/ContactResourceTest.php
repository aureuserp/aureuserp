<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ListPartners as ContactsAppListPartners;
use Webkul\Contact\Models\Partner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\CreateContact;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\EditContact;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ListContacts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ViewContact;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;
use Webkul\Crm\Policies\ContactPolicy;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner as BasePartner;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

/**
 * The Contacts plugin's own permissions for the same partner records.
 *
 * @return array<int, string>
 */
function contactsAppPartnerPermissions(): array
{
    return array_map(
        fn (string $affix): string => "{$affix}_contact_partner",
        CrmTestHelper::ALL_AFFIXES,
    );
}

function crmContact(array $attributes = []): Partner
{
    $partner = BasePartner::factory()->create([
        'account_type' => AccountType::INDIVIDUAL,
        'sub_type'     => 'partner',
        'company_id'   => CrmTestHelper::companyId(),
        ...$attributes,
    ]);

    return Partner::query()->findOrFail($partner->id);
}

/**
 * The receivable/payable accounts the Accounts plugin requires on the contact form, when it is installed.
 *
 * @return array<string, int>
 */
function crmContactAccountingFields(): array
{
    if (! Schema::hasColumn('partners_partners', 'property_account_receivable_id')) {
        return [];
    }

    $account = fn (string $type): ?int => DB::table('accounts_accounts')
        ->where('account_type', $type)
        ->where('deprecated', false)
        ->value('id');

    return array_filter([
        'property_account_receivable_id' => $account('asset_receivable'),
        'property_account_payable_id'    => $account('liability_payable'),
    ]);
}

it('authorizes through the crm contact policy', function () {
    expect(class_uses_recursive(ContactResource::class))->toContain(HasCrmAuthorization::class)
        ->and(ContactResource::getCrmPolicy())->toBe(ContactPolicy::class);
});

it('lists contacts with the crm contact permissions', function () {
    CrmTestHelper::actingAsCrmUser(['contact']);

    $contact = crmContact(['name' => 'Crm Listed Contact']);

    Livewire::test(ListContacts::class)
        ->assertOk()
        ->searchTable('Crm Listed Contact')
        ->assertCanSeeTableRecords([$contact]);
});

it('creates a contact with the crm contact permissions', function () {
    CrmTestHelper::actingAsCrmUser(['contact']);

    Livewire::test(CreateContact::class)
        ->assertOk()
        ->fillForm([
            'account_type' => AccountType::INDIVIDUAL->value,
            'name'         => 'Contact created in CRM',
            'email'        => 'crm-created-contact@example.com',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect(Partner::query()->where('name', 'Contact created in CRM')->where('email', 'crm-created-contact@example.com')->exists())->toBeTrue();
});

it('requires a name to create a contact', function () {
    CrmTestHelper::actingAsCrmUser(['contact']);

    Livewire::test(CreateContact::class)
        ->fillForm(['name' => ''])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('shows a contact with the crm contact permissions', function () {
    CrmTestHelper::actingAsCrmUser(['contact']);

    $contact = crmContact(['name' => 'Crm Viewed Contact']);

    Livewire::test(ViewContact::class, ['record' => $contact->id])
        ->assertOk()
        ->assertSee('Crm Viewed Contact');
});

it('edits a contact with the crm contact permissions', function () {
    CrmTestHelper::actingAsCrmUser(['contact']);

    $contact = crmContact(['name' => 'Crm Contact Before']);

    Livewire::test(EditContact::class, ['record' => $contact->id])
        ->assertOk()
        ->fillForm(['name' => 'Crm Contact After', ...crmContactAccountingFields()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contact->refresh()->name)->toBe('Crm Contact After');
});

it('forbids the crm contact pages with only the contacts app permissions', function (string $page, bool $needsRecord) {
    CrmTestHelper::actingAs(contactsAppPartnerPermissions());

    $parameters = $needsRecord ? ['record' => crmContact()->id] : [];

    Livewire::test($page, $parameters)->assertForbidden();
})->with([
    'list'   => [ListContacts::class, false],
    'create' => [CreateContact::class, false],
    'view'   => [ViewContact::class, true],
    'edit'   => [EditContact::class, true],
]);

it('forbids each crm contact page without its own permission', function (string $page, bool $needsRecord, array $affixes) {
    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['contact'], $affixes));

    $parameters = $needsRecord ? ['record' => crmContact()->id] : [];

    Livewire::test($page, $parameters)->assertForbidden();
})->with([
    'list without view any' => [ListContacts::class, false, ['view', 'create', 'update']],
    'create without create' => [CreateContact::class, false, ['view_any', 'view', 'update']],
    'view without view'     => [ViewContact::class, true, ['view_any', 'create', 'update']],
    'edit without update'   => [EditContact::class, true, ['view_any', 'view', 'create']],
]);

it('does not open the contacts app with only the crm contact permissions', function () {
    CrmTestHelper::actingAsCrmUser(['contact']);

    Livewire::test(ContactsAppListPartners::class)->assertForbidden();
});
