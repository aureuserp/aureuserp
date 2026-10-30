<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ListPartners as ContactsAppListPartners;
use Webkul\Contact\Models\Partner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\CreateCustomer;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\EditCustomer;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\Crm\Filament\Concerns\HasCRMAuthorization;
use Webkul\Crm\Policies\CustomerPolicy;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner as BasePartner;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

/**
 * The Contacts plugin's own permissions for the same partner records.
 *
 * @return array<int, string>
 */
function contactsAppPartnerPermissions(): array
{
    return array_map(
        fn (string $affix): string => "{$affix}_contact_partner",
        CRMHelper::ALL_AFFIXES,
    );
}

function crmContact(array $attributes = []): Partner
{
    $partner = BasePartner::factory()->create([
        'account_type' => AccountType::INDIVIDUAL,
        'sub_type'     => 'partner',
        'company_id'   => CRMHelper::companyId(),
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
    expect(class_uses_recursive(CustomerResource::class))->toContain(HasCRMAuthorization::class)
        ->and(CustomerResource::getCRMPolicy())->toBe(CustomerPolicy::class);
});

it('lists contacts with the crm contact permissions', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    $contact = crmContact(['name' => 'Crm Listed Contact']);

    Livewire::test(ListCustomers::class)
        ->assertOk()
        ->searchTable('Crm Listed Contact')
        ->assertCanSeeTableRecords([$contact]);
});

it('creates a contact with the crm contact permissions', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    Livewire::test(CreateCustomer::class)
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
    CRMHelper::actingAsCRMUser(['customer']);

    Livewire::test(CreateCustomer::class)
        ->fillForm(['name' => ''])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('shows a contact with the crm contact permissions', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    $contact = crmContact(['name' => 'Crm Viewed Contact']);

    Livewire::test(ViewCustomer::class, ['record' => $contact->id])
        ->assertOk()
        ->assertSee('Crm Viewed Contact');
});

it('edits a contact with the crm contact permissions', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    $contact = crmContact(['name' => 'Crm Contact Before']);

    Livewire::test(EditCustomer::class, ['record' => $contact->id])
        ->assertOk()
        ->fillForm(['name' => 'Crm Contact After', ...crmContactAccountingFields()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contact->refresh()->name)->toBe('Crm Contact After');
});

it('forbids the crm contact pages with only the contacts app permissions', function (string $page, bool $needsRecord) {
    CRMHelper::actingAs(contactsAppPartnerPermissions());

    $parameters = $needsRecord ? ['record' => crmContact()->id] : [];

    Livewire::test($page, $parameters)->assertForbidden();
})->with([
    'list'   => [ListCustomers::class, false],
    'create' => [CreateCustomer::class, false],
    'view'   => [ViewCustomer::class, true],
    'edit'   => [EditCustomer::class, true],
]);

it('forbids each crm contact page without its own permission', function (string $page, bool $needsRecord, array $affixes) {
    CRMHelper::actingAs(CRMHelper::permissionsFor(['customer'], $affixes));

    $parameters = $needsRecord ? ['record' => crmContact()->id] : [];

    Livewire::test($page, $parameters)->assertForbidden();
})->with([
    'list without view any' => [ListCustomers::class, false, ['view', 'create', 'update']],
    'create without create' => [CreateCustomer::class, false, ['view_any', 'view', 'update']],
    'view without view'     => [ViewCustomer::class, true, ['view_any', 'create', 'update']],
    'edit without update'   => [EditCustomer::class, true, ['view_any', 'view', 'create']],
]);

it('does not open the contacts app with only the crm contact permissions', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    Livewire::test(ContactsAppListPartners::class)->assertForbidden();
});
