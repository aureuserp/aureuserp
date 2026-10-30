<?php

use Livewire\Livewire;
use Webkul\Contact\Models\Partner;
use Webkul\Crm\Filament\Actions\DownloadCustomersVCardBulkAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Crm\Models\Lead;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner as BasePartner;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function listedSalesperson(array $attributes = []): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => CRMHelper::companyId(),
        'is_active'          => true,
        ...$attributes,
    ]));
}

function listedContact(array $attributes = []): Partner
{
    $partner = BasePartner::factory()->create([
        'account_type' => AccountType::INDIVIDUAL,
        'sub_type'     => 'partner',
        'company_id'   => CRMHelper::companyId(),
        ...$attributes,
    ]);

    return Partner::query()->findOrFail($partner->id);
}

it('shows grid and list tabs with the grid by default', function (array $query, array $active) {
    CRMHelper::actingAsCRMUser(['customer']);

    $contact = listedContact(['name' => 'Tabbed Contact']);

    $component = Livewire::withQueryParams($query)->test(ListCustomers::class)->assertOk();

    expect(collect($component->instance()->getRecordNavigationTabs())->map(fn ($tab): bool => $tab->isActive())->all())->toBe($active);

    $component->searchTable('Tabbed Contact')->assertCanSeeTableRecords([$contact]);
})->with([
    'grid' => [[], [true, false]],
    'list' => [['layout' => 'list'], [false, true]],
    'bad'  => [['layout' => 'nope'], [true, false]],
]);

it('shows the grid with the same customer card as the core partners grid', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    $contact = listedContact(['name' => 'Card Contact', 'email' => 'card.contact@example.com', 'job_title' => 'Buyer']);

    Livewire::test(ListCustomers::class)
        ->assertTableColumnExists('avatar')
        ->assertTableColumnExists('job_title')
        ->assertTableColumnExists('email')
        ->assertTableColumnExists('phone')
        ->assertCanSeeTableRecords([$contact])
        ->assertSee('card.contact@example.com');
});

it('offers the full bulk menu', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    $component = Livewire::test(ListCustomers::class);

    foreach (['export', 'archiveRecords', 'restore', 'duplicateRecords', 'forceDelete', 'mergeCustomers', 'sendCustomersEmail', 'sendCustomersSMS', 'downloadCustomersVCard'] as $name) {
        $component->assertTableBulkActionExists($name);
    }
});

it('shows a kanban board with one column per salesperson when grouped', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    $salesperson = listedSalesperson(['name' => 'Board Salesperson']);
    $contact = listedContact(['name' => 'Board Contact', 'user_id' => $salesperson->id]);

    $component = Livewire::test(ListCustomers::class)->set('tableGrouping', 'user.name');

    $columns = collect($component->instance()->getKanbanColumns());
    $column = $columns->firstWhere('value', (string) $salesperson->id);

    expect($column?->label)->toBe('Board Salesperson')
        ->and($column->records->modelKeys())->toContain($contact->id)
        ->and($component->instance()->getTableRecords())->toHaveCount(0);
});

it('moves a contact to another salesperson on the board but not between company types', function () {
    CRMHelper::actingAsCRMUser(['customer']);

    $from = listedSalesperson();
    $to = listedSalesperson();
    listedContact(['user_id' => $to->id]);
    $contact = listedContact(['user_id' => $from->id]);

    Livewire::test(ListCustomers::class)
        ->set('tableGrouping', 'user.name')
        ->call('moveRecord', (string) $contact->id, (string) $to->id, []);

    expect($contact->refresh()->user_id)->toBe($to->id);

    Livewire::test(ListCustomers::class)
        ->set('tableGrouping', 'account_type')
        ->call('moveRecord', (string) $contact->id, AccountType::COMPANY->value, []);

    expect($contact->refresh()->account_type)->toBe(AccountType::INDIVIDUAL);
});

it('merges contacts into the one kept and moves their opportunities', function () {
    CRMHelper::actingAsCRMUser(['contact', 'opportunity']);

    $kept = listedContact(['name' => 'Kept Contact', 'phone' => null]);
    $merged = listedContact(['name' => 'Merged Contact', 'phone' => '+91 98765 43210']);
    $opportunity = CRMHelper::opportunity(['partner_id' => $merged->id]);

    Livewire::test(ListCustomers::class)
        ->callTableBulkAction('mergeCustomers', [$kept, $merged], ['destination' => $kept->id])
        ->assertHasNoTableBulkActionErrors();

    expect($opportunity->refresh()->partner_id)->toBe($kept->id)
        ->and($kept->refresh()->phone)->toBe('+91 98765 43210')
        ->and(BasePartner::withTrashed()->find($merged->id)->trashed())->toBeTrue()
        ->and(Lead::query()->where('partner_id', $merged->id)->exists())->toBeFalse();
});

it('builds a vCard for a contact', function () {
    $company = listedContact(['name' => 'Card Company', 'account_type' => AccountType::COMPANY]);
    $contact = listedContact([
        'name'      => 'Card Person',
        'parent_id' => $company->id,
        'job_title' => 'Buyer',
        'email'     => 'card@acme.test',
        'phone'     => '+1 555 0100',
        'city'      => 'Paris',
    ]);

    $vCard = DownloadCustomersVCardBulkAction::vCard($contact);

    expect($vCard)->toStartWith("BEGIN:VCARD\r\nVERSION:3.0\r\n")
        ->toContain("FN:Card Person\r\n")
        ->toContain("ORG:Card Company\r\n")
        ->toContain("TITLE:Buyer\r\n")
        ->toContain("EMAIL;TYPE=INTERNET:card@acme.test\r\n")
        ->toContain("TEL;TYPE=WORK,VOICE:+1 555 0100\r\n")
        ->toContain(';Paris;')
        ->toEndWith("END:VCARD\r\n");
});
