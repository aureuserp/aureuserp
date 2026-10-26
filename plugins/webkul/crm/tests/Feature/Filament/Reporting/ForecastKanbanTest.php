<?php

use Illuminate\Support\Number;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\KanbanForecast;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityType;
use Wezlo\FilamentKanban\KanbanColumn;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

/**
 * The `YYYY-MM` month a number of months from the current one.
 */
function forecastMonth(int $offset): string
{
    return today()->startOfMonth()->addMonths($offset)->format('Y-m');
}

/**
 * The months a number of months from the current one, from `$from` to `$to`.
 *
 * @return array<int, string>
 */
function forecastMonths(int $from, int $to): array
{
    return array_map(fn (int $offset): string => forecastMonth($offset), range($from, $to));
}

/**
 * @return array<int, KanbanColumn>
 */
function forecastColumns(mixed $component): array
{
    return $component->instance()->getKanbanColumns();
}

function forecastColumn(mixed $component, string $value): ?KanbanColumn
{
    return collect(forecastColumns($component))->firstWhere('value', $value);
}

function forecastUser(): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => CrmTestHelper::companyId(),
        'is_active'          => true,
    ]));
}

it('is the index page of the forecast', function () {
    expect(ForecastResource::getPages()['index']->getPage())->toBe(KanbanForecast::class);
});

it('renders with the forecast permission and is forbidden without', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    Livewire::test(KanbanForecast::class)->assertOk();

    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['opportunity']));

    Livewire::test(KanbanForecast::class)->assertForbidden();
});

it('starts without a default filter', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    expect(Livewire::test(KanbanForecast::class)->get('kanbanFilters'))->toBe([
        'user_id' => null,
        'team_id' => null,
        'period'  => 'all',
        'presets' => [],
    ]);
});

it('shows the months with opportunities plus the current and next three months', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    CrmTestHelper::opportunity(['expected_closing_date' => today()->startOfMonth()->addMonths(6)->addDays(9)]);
    CrmTestHelper::opportunity(['expected_closing_date' => today()->startOfMonth()->addMonths(1)]);

    $values = collect(forecastColumns(Livewire::test(KanbanForecast::class)))->pluck('value')->all();

    expect($values)->toBe([...forecastMonths(0, 3), forecastMonth(6)]);
});

it('labels the columns by month and highlights the current month', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $columns = forecastColumns(Livewire::test(KanbanForecast::class));

    expect($columns[0]->value)->toBe(forecastMonth(0))
        ->and($columns[0]->label)->toBe(Dimension::monthLabel(forecastMonth(0)))
        ->and($columns[0]->color)->toBe('primary')
        ->and($columns[1]->color)->toBe('gray');
});

it('hides the "None" column in upcoming mode', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    CrmTestHelper::opportunity(['expected_closing_date' => null]);

    $values = collect(forecastColumns(Livewire::test(KanbanForecast::class)))->pluck('value')->all();

    expect($values)->not->toContain('none')
        ->and($values)->toBe(forecastMonths(0, 3));
});

it('shows "None" first and past months when every date is shown', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $undated = CrmTestHelper::opportunity(['expected_closing_date' => null]);
    $past = CrmTestHelper::opportunity(['expected_closing_date' => today()->startOfMonth()->subMonths(2)]);

    $component = Livewire::test(KanbanForecast::class)->set('kanbanFilters.period', 'all');

    $columns = forecastColumns($component);

    expect(collect($columns)->pluck('value')->all())->toBe(['none', forecastMonth(-2), ...forecastMonths(0, 3)])
        ->and($columns[0]->label)->toBe('None')
        ->and($columns[0]->records->modelKeys())->toBe([$undated->id])
        ->and(forecastColumn($component, forecastMonth(-2))->records->modelKeys())->toBe([$past->id]);
});

it('shows an empty "None" column when every date is shown', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $component = Livewire::test(KanbanForecast::class)->set('kanbanFilters.period', 'all');

    expect(forecastColumn($component, 'none'))->not->toBeNull()
        ->and(forecastColumn($component, 'none')->count)->toBe(0);
});

it('puts each opportunity in the column of its expected closing month', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $thisMonth = CrmTestHelper::opportunity(['expected_closing_date' => today()->startOfMonth()]);
    $nextMonth = CrmTestHelper::opportunity(['expected_closing_date' => today()->startOfMonth()->addMonth()->endOfMonth()]);

    $component = Livewire::test(KanbanForecast::class);

    expect(forecastColumn($component, forecastMonth(0))->records->modelKeys())->toBe([$thisMonth->id])
        ->and(forecastColumn($component, forecastMonth(1))->records->modelKeys())->toBe([$nextMonth->id])
        ->and(forecastColumn($component, forecastMonth(1))->count)->toBe(1);
});

it('leaves out leads, lost opportunities and other salespeople', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $date = today()->startOfMonth()->addMonth();

    $mine = CrmTestHelper::opportunity(['expected_closing_date' => $date]);
    $lead = CrmTestHelper::lead(['expected_closing_date' => $date]);
    $lost = CrmTestHelper::opportunity(['expected_closing_date' => $date]);
    $lost->markAsLost(null);
    $theirs = CrmTestHelper::opportunity(['expected_closing_date' => $date, 'user_id' => forecastUser()->id]);

    $component = Livewire::test(KanbanForecast::class);

    expect(forecastColumn($component, forecastMonth(1))->records->modelKeys())->toBe([$mine->id]);

    $component->set('kanbanFilters.user_id', null);

    expect(forecastColumn($component, forecastMonth(1))->records->modelKeys())
        ->toContain($mine->id, $theirs->id)
        ->not->toContain($lead->id)
        ->not->toContain($lost->id);
});

it('clears every filter on reset', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    expect(Livewire::test(KanbanForecast::class)->call('resetKanbanFilters')->get('kanbanFilters'))->toBe([
        'user_id' => null,
        'team_id' => null,
        'period'  => 'all',
        'presets' => [],
    ]);
});

it('searches opportunities by name', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $date = today()->startOfMonth()->addMonth();

    $match = CrmTestHelper::opportunity(['name' => 'Forecast Searchable Deal', 'expected_closing_date' => $date]);
    $other = CrmTestHelper::opportunity(['name' => 'Another Deal', 'expected_closing_date' => $date]);

    $component = Livewire::test(KanbanForecast::class)->set('kanbanSearch', 'Forecast Searchable');

    expect(forecastColumn($component, forecastMonth(1))->records->modelKeys())->toBe([$match->id])
        ->not->toContain($other->id);
});

it('sums the prorated revenue of each column', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $date = today()->startOfMonth()->addMonth();

    CrmTestHelper::opportunity(['expected_closing_date' => $date, 'expected_revenue' => 1000, 'probability' => 50]);
    CrmTestHelper::opportunity(['expected_closing_date' => $date, 'expected_revenue' => 2000, 'probability' => 25]);

    $component = Livewire::test(KanbanForecast::class);
    $board = $component->instance()->getKanbanBoard();

    expect($board->resolveColumnSummary(forecastColumn($component, forecastMonth(1))))
        ->toBe(Number::currency(1000, Report::getCurrencyCode()))
        ->and($board->resolveColumnSummary(forecastColumn($component, forecastMonth(2))))
        ->toBeNull();
});

it('moves the expected closing date to the end of the target month', function (string $month, string $expectedDate) {
    CrmTestHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity']);

    $opportunity = CrmTestHelper::opportunity(['expected_closing_date' => today()->startOfMonth()]);

    Livewire::test(KanbanForecast::class)->call('moveRecord', (string) $opportunity->id, $month, []);

    expect($opportunity->fresh()->expected_closing_date->toDateString())->toBe($expectedDate);
})->with([
    'february 2027' => ['2027-02', '2027-02-28'],
    'leap february' => ['2028-02', '2028-02-29'],
    'december'      => ['2026-12', '2026-12-31'],
    'thirty days'   => ['2027-04', '2027-04-30'],
]);

it('clears the expected closing date when moved to "None"', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity']);

    $opportunity = CrmTestHelper::opportunity(['expected_closing_date' => today()->startOfMonth()]);

    Livewire::test(KanbanForecast::class)->call('moveRecord', (string) $opportunity->id, 'none', []);

    expect($opportunity->fresh()->expected_closing_date)->toBeNull();
});

it('ignores invalid target columns', function (string $value) {
    CrmTestHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity']);

    $date = today()->startOfMonth()->addDays(4)->toDateString();
    $opportunity = CrmTestHelper::opportunity(['expected_closing_date' => $date]);

    Livewire::test(KanbanForecast::class)->call('moveRecord', (string) $opportunity->id, $value, []);

    expect($opportunity->fresh()->expected_closing_date->toDateString())->toBe($date);
})->with([
    'month 13'       => ['2027-13'],
    'month 00'       => ['2027-00'],
    'not a month'    => ['abc'],
    'full date'      => ['2027-02-15'],
    'empty'          => [''],
    'sql injection'  => ["2027-02' or 1=1 --"],
]);

it('refuses to move an opportunity without the opportunity update permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast', 'update_crm_lead']);

    $date = today()->startOfMonth()->toDateString();
    $opportunity = CrmTestHelper::opportunity(['expected_closing_date' => $date]);

    Livewire::test(KanbanForecast::class)->call('moveRecord', (string) $opportunity->id, '2027-02', []);

    expect($opportunity->fresh()->expected_closing_date->toDateString())->toBe($date);
});

it('refuses to move a lost opportunity', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity']);

    $date = today()->startOfMonth()->toDateString();
    $opportunity = CrmTestHelper::opportunity(['expected_closing_date' => $date]);
    $opportunity->markAsLost(null);

    Livewire::test(KanbanForecast::class)->call('moveRecord', (string) $opportunity->id, '2027-02', []);

    expect(Lead::withTrashed()->findOrFail($opportunity->id)->expected_closing_date->toDateString())->toBe($date);
});

it('refuses to move a lead', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity', 'update_crm_lead']);

    $date = today()->startOfMonth()->toDateString();
    $lead = CrmTestHelper::lead(['expected_closing_date' => $date]);

    Livewire::test(KanbanForecast::class)->call('moveRecord', (string) $lead->id, '2027-02', []);

    expect($lead->fresh()->expected_closing_date->toDateString())->toBe($date);
});

it('has no "Add" column', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast', ...CrmTestHelper::permissionsFor(['stage'])]);

    $component = Livewire::test(KanbanForecast::class)->assertDontSee('Add Stage');

    expect(method_exists($component->instance(), 'canAddKanbanColumn'))->toBeFalse();
});

it('offers the other report views as toggle buttons in the header', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    Livewire::test(KanbanForecast::class)
        ->assertActionDoesNotExist('view_kanban')
        ->assertActionExists('view_graph')
        ->assertActionHasColor('view_graph', 'gray')
        ->assertActionExists('view_summary')
        ->assertActionExists('view_list');
});

it('applies the list toggle filters (won, lost) on the board', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $won = CrmTestHelper::opportunity(['stage_id' => CrmTestHelper::wonStage()->id, 'expected_closing_date' => now()->addMonth()->toDateString()]);
    $lost = CrmTestHelper::opportunity(['expected_closing_date' => now()->addMonth()->toDateString()]);
    $lost->markAsLost(null);

    $ids = fn (array $presets): array => collect(forecastColumns(Livewire::test(KanbanForecast::class)
        ->set('kanbanFilters.user_id', null)
        ->set('kanbanFilters.period', 'all')
        ->set('kanbanFilters.presets', $presets)))
        ->flatMap(fn (KanbanColumn $column): array => $column->records->modelKeys())
        ->all();

    expect($ids([]))->toContain($won->id)->not->toContain($lost->id)
        ->and($ids(['lost']))->toContain($lost->id)->not->toContain($won->id)
        ->and($ids(['won']))->toContain($won->id)->not->toContain($lost->id);
});

it('shows the activity progress of each month and filters a month from it', function () {
    $user = CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $month = forecastMonth(1);
    $closing = today()->startOfMonth()->addMonth()->addDays(5)->toDateString();

    $overdue = CrmTestHelper::opportunity(['expected_closing_date' => $closing]);
    $overdue->addMessage([
        'type'             => 'activity',
        'activity_type_id' => ActivityType::query()->where('plugin', 'crm')->value('id'),
        'date_deadline'    => now()->subDay()->toDateString(),
        'assigned_to'      => $user->id,
    ]);
    $quiet = CrmTestHelper::opportunity(['expected_closing_date' => $closing]);

    $component = Livewire::test(KanbanForecast::class);
    $progress = $component->instance()->kanbanColumnProgress(forecastColumn($component, $month));

    expect($progress['overdue'])->toBeGreaterThanOrEqual(1)
        ->and($progress['none'])->toBeGreaterThanOrEqual(1);

    $component->call('toggleKanbanActivityFilter', $month, 'overdue');

    expect(forecastColumn($component, $month)->records->modelKeys())
        ->toContain($overdue->id)
        ->not->toContain($quiet->id);
});

it('marks won and lost opportunities with a ribbon', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $won = CrmTestHelper::opportunity([
        'expected_closing_date' => today()->addMonth()->toDateString(),
        'stage_id'              => Stage::query()->where('is_won', true)->value('id'),
    ]);

    Livewire::test(KanbanForecast::class)
        ->assertSee($won->name)
        ->assertSeeHtml('bg-success-600');
});
