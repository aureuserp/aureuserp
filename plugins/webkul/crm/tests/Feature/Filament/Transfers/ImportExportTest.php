<?php

use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Actions\CrmExportAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\KanbanLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\KanbanOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Crm\Filament\Exports\LeadExporter;
use Webkul\Crm\Filament\Exports\OpportunityExporter;
use Webkul\Crm\Filament\Exports\QuotationExporter;
use Webkul\Crm\Filament\Imports\LeadImporter;
use Webkul\Crm\Filament\Imports\OpportunityImporter;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

function transferOtherCompany(): Company
{
    $company = Company::factory()->create();

    auth()->user()?->allowedCompanies()->detach($company->id);

    return $company;
}

function transferImportRow(string $importer, array $row, ?User $user = null): Lead
{
    $import = Import::query()->create([
        'file_name'  => 'test.csv',
        'file_path'  => 'test.csv',
        'importer'   => $importer,
        'total_rows' => 1,
        'user_id'    => ($user ?? auth()->user())->getKey(),
    ]);

    $columnMap = collect($importer::getColumns())
        ->mapWithKeys(fn ($column): array => [$column->getName() => $column->getName()])
        ->all();

    $instance = new $importer($import, $columnMap, []);

    $instance([...array_fill_keys(array_keys($columnMap), null), ...$row]);

    return $instance->getRecord()->fresh();
}

function transferExportRow(string $exporter, $record): array
{
    $export = Export::query()->create([
        'exporter'   => $exporter,
        'total_rows' => 1,
        'file_disk'  => 'local',
        'user_id'    => auth()->id(),
    ]);

    $columnMap = collect($exporter::getColumns())
        ->mapWithKeys(fn ($column): array => [$column->getName() => $column->getLabel()])
        ->all();

    $row = (new $exporter($export, $columnMap, []))($exporter::modifyQuery($record::query())->find($record->getKey()));

    return array_combine(array_keys($columnMap), $row);
}

it('shows import and export next to the create button on the leads and pipeline lists', function (string $page) {
    CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

    Livewire::test($page)
        ->assertActionVisible('import')
        ->assertActionVisible('export')
        ->assertActionVisible('create');
})->with([
    'leads'    => ListLeads::class,
    'pipeline' => ListOpportunities::class,
]);

it('shows import and export on the leads and pipeline kanban boards', function (string $page) {
    CrmTestHelper::actingAsCrmUser(['lead', 'opportunity', 'stage']);

    Livewire::test($page)
        ->assertActionVisible('import')
        ->assertActionVisible('export')
        ->assertActionVisible('create');
})->with([
    'leads board'    => KanbanLeads::class,
    'pipeline board' => KanbanOpportunities::class,
]);

it('limits a kanban export to the board filters', function () {
    $user = CrmTestHelper::actingAsCrmUser(['opportunity', 'stage']);

    $mine = CrmTestHelper::opportunity(['name' => 'Board export mine', 'user_id' => $user->id]);
    $otherUser = User::withoutEvents(fn (): User => User::factory()->create(['default_company_id' => CrmTestHelper::companyId(), 'is_active' => true]));

    $someoneElse = CrmTestHelper::opportunity(['name' => 'Board export other', 'user_id' => $otherUser->id]);

    $page = Livewire::test(KanbanOpportunities::class)->instance();

    $boardIds = (fn () => $this->getKanbanQuery($this->getKanbanBoard()))->call($page)->pluck('crm_leads.id');

    expect($page->kanbanFilters['user_id'])->toBe($user->id)
        ->and($boardIds)->toContain($mine->id)
        ->and($boardIds)->not->toContain($someoneElse->id);
});

it('hides import without the create permission', function () {
    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['opportunity'], ['view_any', 'view']));

    Livewire::test(ListOpportunities::class)
        ->assertActionHidden('import')
        ->assertActionVisible('export');
});

it('offers export only on my quotations', function () {
    CrmTestHelper::actingAsCrmUser(['quotation']);

    Livewire::test(ListQuotations::class)
        ->assertActionVisible('export')
        ->assertActionDoesNotExist('import');
});

it('pins an export to the records visible in the request', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $visible = CrmTestHelper::opportunity(['name' => 'Visible export opp']);

    $query = CrmExportAction::limitToVisibleRecords(Lead::query()->whereKey($visible->getKey()));

    $foreign = Lead::withoutGlobalScopes()->forceCreate([
        'type'       => Lead::TYPE_OPPORTUNITY,
        'name'       => 'Created after the export started',
        'company_id' => transferOtherCompany()->id,
    ]);

    expect($query->withoutGlobalScopes()->pluck('id')->all())
        ->toContain($visible->id)
        ->not->toContain($foreign->id);
});

it('creates an opportunity from an imported row with its related records', function () {
    $user = CrmTestHelper::actingAsCrmUser(['opportunity']);

    $team = Team::factory()->create(['name' => 'Imported team (test)', 'company_id' => CrmTestHelper::companyId()]);
    $partner = Partner::factory()->create(['name' => 'Imported customer (test)', 'company_id' => CrmTestHelper::companyId()]);
    $stage = Stage::factory()->create(['name' => 'Imported stage (test)']);

    $lead = transferImportRow(OpportunityImporter::class, [
        'name'                  => 'Imported opportunity',
        'partner'               => $partner->name,
        'expected_revenue'      => '5000',
        'expected_closing_date' => '2026-12-31',
        'stage'                 => $stage->name,
        'team'                  => $team->name,
        'user'                  => $user->email,
        'priority'              => 'High',
        'probability'           => '40',
        'tags'                  => 'Imported A, Imported B',
        'email'                 => 'buyer@example.com',
    ]);

    expect($lead->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($lead->company_id)->toBe(CrmTestHelper::companyId())
        ->and($lead->creator_id)->toBe($user->id)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->partner_id)->toBe($partner->id)
        ->and($lead->team_id)->toBe($team->id)
        ->and($lead->stage_id)->toBe($stage->id)
        ->and((float) $lead->expected_revenue)->toBe(5000.0)
        ->and($lead->expected_closing_date->toDateString())->toBe('2026-12-31')
        ->and($lead->priority)->toBe(OpportunityPriority::High)
        ->and($lead->tags->pluck('name')->sort()->values()->all())->toBe(['Imported A', 'Imported B']);
});

it('creates a lead with the lead defaults when cells are empty', function () {
    $user = CrmTestHelper::actingAsCrmUser(['lead']);

    $lead = transferImportRow(LeadImporter::class, ['name' => 'Imported lead', 'contact_name' => 'Jo Doe']);

    expect($lead->type)->toBe(Lead::TYPE_LEAD)
        ->and((float) $lead->probability)->toBe(10.0)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->stage_id)->not->toBeNull();
});

it('updates the record of a row that has its id', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $opportunity = CrmTestHelper::opportunity(['name' => 'Before import', 'expected_revenue' => 100, 'city' => 'Pune']);

    $updated = transferImportRow(OpportunityImporter::class, [
        'id'               => (string) $opportunity->id,
        'name'             => 'After import',
        'expected_revenue' => '7500',
    ]);

    expect($updated->id)->toBe($opportunity->id)
        ->and($updated->name)->toBe('After import')
        ->and((float) $updated->expected_revenue)->toBe(7500.0)
        ->and($updated->city)->toBe('Pune')
        ->and(Lead::query()->where('name', 'After import')->count())->toBe(1);
});

it('fails a row whose id is unknown, another type or another company', function (Closure $makeId) {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $id = $makeId();

    expect(fn () => transferImportRow(OpportunityImporter::class, ['id' => (string) $id, 'name' => 'Hijacked']))
        ->toThrow(RowImportFailedException::class);

    expect(Lead::withoutGlobalScopes()->where('name', 'Hijacked')->exists())->toBeFalse();
})->with([
    'unknown id'      => fn () => fn (): int => 99999999,
    'a lead id'       => fn () => fn (): int => CrmTestHelper::lead()->id,
    'another company' => fn () => fn (): int => Lead::withoutGlobalScopes()->forceCreate([
        'type'       => Lead::TYPE_OPPORTUNITY,
        'name'       => 'Other company opportunity',
        'company_id' => transferOtherCompany()->id,
    ])->id,
]);

it('fails a row that names an unknown team, salesperson, stage or customer', function (string $column) {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    expect(fn () => transferImportRow(OpportunityImporter::class, ['name' => 'Unknown relation', $column => 'Does not exist (test)']))
        ->toThrow(RowImportFailedException::class);
})->with(['team', 'user', 'stage', 'partner', 'country', 'priority']);

it('does not accept a team or customer of another company', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $otherCompany = transferOtherCompany();

    $team = Team::factory()->create(['name' => 'Foreign team (test)', 'company_id' => $otherCompany->id]);

    expect(fn () => transferImportRow(OpportunityImporter::class, ['name' => 'Foreign team row', 'team' => $team->name]))
        ->toThrow(RowImportFailedException::class);
});

it('rejects an invalid email', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    expect(fn () => transferImportRow(OpportunityImporter::class, ['name' => 'Bad email', 'email' => 'not-an-email']))
        ->toThrow(ValidationException::class);
});

it('checks the create and update permissions of the user who started the import', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $opportunity = CrmTestHelper::opportunity();

    $readOnly = CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['opportunity'], ['view_any', 'view']));

    expect(fn () => transferImportRow(OpportunityImporter::class, ['name' => 'Not allowed'], $readOnly))
        ->toThrow(RowImportFailedException::class)
        ->and(fn () => transferImportRow(OpportunityImporter::class, ['id' => (string) $opportunity->id, 'name' => 'Not allowed'], $readOnly))
        ->toThrow(RowImportFailedException::class);
});

it('exports an opportunity with labels that the importer reads back', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $opportunity = CrmTestHelper::opportunity([
        'name'                  => 'Round trip opp',
        'expected_revenue'      => 1234,
        'expected_closing_date' => '2026-11-30',
        'priority'              => OpportunityPriority::High,
    ]);

    $row = transferExportRow(OpportunityExporter::class, $opportunity);

    expect($row['id'])->toBe((string) $opportunity->id)
        ->and($row['name'])->toBe('Round trip opp')
        ->and($row['expected_closing_date'])->toBe('2026-11-30')
        ->and($row['priority'])->toBe(OpportunityPriority::High->getLabel());

    $reimported = transferImportRow(OpportunityImporter::class, [
        'id'       => $row['id'],
        'name'     => $row['name'].' (edited)',
        'priority' => $row['priority'],
        'stage'    => $row['stage.name'],
    ]);

    expect($reimported->id)->toBe($opportunity->id)
        ->and($reimported->name)->toBe('Round trip opp (edited)')
        ->and($reimported->priority)->toBe(OpportunityPriority::High);
});

it('exports a lead without opportunity-only columns', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $row = transferExportRow(LeadExporter::class, CrmTestHelper::lead(['name' => 'Exported lead']));

    expect($row['name'])->toBe('Exported lead')
        ->and($row)->not->toHaveKey('expected_revenue');
});

it('exports quotations with totals and translated statuses', function () {
    expect(collect(QuotationExporter::getColumns())->map->getName()->all())
        ->toContain('name', 'partner.name', 'amount_untaxed', 'amount_tax', 'amount_total', 'state', 'invoice_status');
});
