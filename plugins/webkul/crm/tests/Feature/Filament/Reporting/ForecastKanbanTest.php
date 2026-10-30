<?php

use Illuminate\Support\Number;
use Livewire\Livewire;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\ListForecast;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityType;
use Wezlo\FilamentKanban\KanbanColumn;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

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
        'default_company_id' => CRMHelper::companyId(),
        'is_active'          => true,
    ]));
}

it('is the index page of the forecast', function () {
    expect(ForecastResource::getPages()['index']->getPage())->toBe(ListForecast::class);
});

it('renders with the forecast permission and is forbidden without', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    Livewire::test(ListForecast::class)->assertOk();

    CRMHelper::actingAs(CRMHelper::permissionsFor(['opportunity']));

    Livewire::test(ListForecast::class)->assertForbidden();
});

it('starts without a default filter', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    Livewire::test(ListForecast::class)->assertSet('activeTableView', 'default');
});

it('shows the months with opportunities plus the current and next three months', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    CRMHelper::opportunity(['expected_closing_date' => today()->startOfMonth()->addMonths(6)->addDays(9)]);
    CRMHelper::opportunity(['expected_closing_date' => today()->startOfMonth()->addMonths(1)]);

    $values = collect(forecastColumns(Livewire::test(ListForecast::class)))->pluck('value')->all();

    expect($values)->toBe([...forecastMonths(0, 3), forecastMonth(6)]);
});

it('labels the columns by month and highlights the current month', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $columns = forecastColumns(Livewire::test(ListForecast::class));

    expect($columns[0]->value)->toBe(forecastMonth(0))
        ->and($columns[0]->label)->toBe(Dimension::monthLabel(forecastMonth(0)))
        ->and($columns[0]->color)->toBe('primary')
        ->and($columns[1]->color)->toBe('gray');
});

it('shows the pipeline tabs and filters the months by the chosen tab', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $date = today()->startOfMonth()->addMonth();

    $open = CRMHelper::opportunity(['expected_closing_date' => $date]);
    $lost = CRMHelper::opportunity(['expected_closing_date' => $date]);
    $lost->markAsLost(null);

    $component = Livewire::test(ListForecast::class);

    expect(array_keys($component->instance()->getCachedFavoriteTableViews()))->toContain('default', 'open', 'won', 'lost');

    $component->call('loadView', 'lost');

    expect(forecastColumn($component, forecastMonth(1))->records->modelKeys())->toBe([$lost->id])
        ->not->toContain($open->id);
});

it('shows "None" first, then the current month, and leaves out past months', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $undated = CRMHelper::opportunity(['expected_closing_date' => null]);
    $past = CRMHelper::opportunity(['expected_closing_date' => today()->startOfMonth()->subMonths(2)]);

    $component = Livewire::test(ListForecast::class);

    $columns = forecastColumns($component);
    $keys = collect($columns)->flatMap(fn ($column) => $column->records->modelKeys())->all();

    expect(collect($columns)->pluck('value')->all())->toBe(['none', ...forecastMonths(0, 3)])
        ->and($columns[0]->label)->toBe('None')
        ->and($columns[0]->records->modelKeys())->toBe([$undated->id])
        ->and($keys)->not->toContain($past->id);
});

it('shows an empty "None" column when every date is shown', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $component = Livewire::test(ListForecast::class);

    expect(forecastColumn($component, 'none'))->not->toBeNull()
        ->and(forecastColumn($component, 'none')->count)->toBe(0);
});

it('puts each opportunity in the column of its expected closing month', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $thisMonth = CRMHelper::opportunity(['expected_closing_date' => today()->startOfMonth()]);
    $nextMonth = CRMHelper::opportunity(['expected_closing_date' => today()->startOfMonth()->addMonth()->endOfMonth()]);

    $component = Livewire::test(ListForecast::class);

    expect(forecastColumn($component, forecastMonth(0))->records->modelKeys())->toBe([$thisMonth->id])
        ->and(forecastColumn($component, forecastMonth(1))->records->modelKeys())->toBe([$nextMonth->id])
        ->and(forecastColumn($component, forecastMonth(1))->count)->toBe(1);
});

it('leaves out leads, lost opportunities and other salespeople', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $date = today()->startOfMonth()->addMonth();

    $mine = CRMHelper::opportunity(['expected_closing_date' => $date]);
    $lead = CRMHelper::lead(['expected_closing_date' => $date]);
    $lost = CRMHelper::opportunity(['expected_closing_date' => $date]);
    $lost->markAsLost(null);
    $theirs = CRMHelper::opportunity(['expected_closing_date' => $date, 'user_id' => forecastUser()->id]);

    $component = Livewire::test(ListForecast::class);

    expect(forecastColumn($component, forecastMonth(1))->records->modelKeys())
        ->toContain($mine->id, $theirs->id)
        ->not->toContain($lead->id)
        ->not->toContain($lost->id);
});

it('filters the months with the core salesperson rule', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $date = today()->startOfMonth()->addMonth();

    $salesperson = forecastUser();

    $mine = CRMHelper::opportunity(['expected_closing_date' => $date]);
    $theirs = CRMHelper::opportunity(['expected_closing_date' => $date, 'user_id' => $salesperson->id]);

    $component = Livewire::test(ListForecast::class)->set('tableFilters', ['queryBuilder' => ['rules' => ['user' => [
        'type' => 'user',
        'data' => ['operator' => 'isRelatedTo', 'settings' => ['values' => [(string) $salesperson->id]]],
    ]]]]);

    expect(forecastColumn($component, forecastMonth(1))->records->modelKeys())->toBe([$theirs->id])
        ->not->toContain($mine->id);
});

it('searches opportunities by name', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $date = today()->startOfMonth()->addMonth();

    $match = CRMHelper::opportunity(['name' => 'Forecast Searchable Deal', 'expected_closing_date' => $date]);
    $other = CRMHelper::opportunity(['name' => 'Another Deal', 'expected_closing_date' => $date]);

    $component = Livewire::test(ListForecast::class)->set('tableSearch', 'Forecast Searchable');

    expect(forecastColumn($component, forecastMonth(1))->records->modelKeys())->toBe([$match->id])
        ->not->toContain($other->id);
});

it('sums the prorated revenue of each column', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $date = today()->startOfMonth()->addMonth();

    CRMHelper::opportunity(['expected_closing_date' => $date, 'expected_revenue' => 1000, 'probability' => 50]);
    CRMHelper::opportunity(['expected_closing_date' => $date, 'expected_revenue' => 2000, 'probability' => 25]);

    $component = Livewire::test(ListForecast::class);
    $board = $component->instance()->getKanbanBoard();

    expect($board->resolveColumnSummary(forecastColumn($component, forecastMonth(1))))
        ->toBe(Number::currency(1000, Report::getCurrencyCode()))
        ->and($board->resolveColumnSummary(forecastColumn($component, forecastMonth(2))))
        ->toBeNull();
});

it('moves the expected closing date to the end of the target month', function (string $month, string $expectedDate) {
    CRMHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity']);

    $opportunity = CRMHelper::opportunity(['expected_closing_date' => today()->startOfMonth()]);

    Livewire::test(ListForecast::class)->call('moveRecord', (string) $opportunity->id, $month, []);

    expect($opportunity->fresh()->expected_closing_date->toDateString())->toBe($expectedDate);
})->with([
    'february 2027' => ['2027-02', '2027-02-28'],
    'leap february' => ['2028-02', '2028-02-29'],
    'december'      => ['2026-12', '2026-12-31'],
    'thirty days'   => ['2027-04', '2027-04-30'],
]);

it('clears the expected closing date when moved to "None"', function () {
    CRMHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity']);

    $opportunity = CRMHelper::opportunity(['expected_closing_date' => today()->startOfMonth()]);

    Livewire::test(ListForecast::class)->call('moveRecord', (string) $opportunity->id, 'none', []);

    expect($opportunity->fresh()->expected_closing_date)->toBeNull();
});

it('ignores invalid target columns', function (string $value) {
    CRMHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity']);

    $date = today()->startOfMonth()->addDays(4)->toDateString();
    $opportunity = CRMHelper::opportunity(['expected_closing_date' => $date]);

    Livewire::test(ListForecast::class)->call('moveRecord', (string) $opportunity->id, $value, []);

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
    CRMHelper::actingAs(['view_any_crm_forecast', 'update_crm_lead']);

    $date = today()->startOfMonth()->toDateString();
    $opportunity = CRMHelper::opportunity(['expected_closing_date' => $date]);

    Livewire::test(ListForecast::class)->call('moveRecord', (string) $opportunity->id, '2027-02', []);

    expect($opportunity->fresh()->expected_closing_date->toDateString())->toBe($date);
});

it('refuses to move a lost opportunity', function () {
    CRMHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity']);

    $date = today()->startOfMonth()->toDateString();
    $opportunity = CRMHelper::opportunity(['expected_closing_date' => $date]);
    $opportunity->markAsLost(null);

    Livewire::test(ListForecast::class)->call('moveRecord', (string) $opportunity->id, '2027-02', []);

    expect(Lead::withTrashed()->findOrFail($opportunity->id)->expected_closing_date->toDateString())->toBe($date);
});

it('refuses to move a lead', function () {
    CRMHelper::actingAs(['view_any_crm_forecast', 'update_crm_opportunity', 'update_crm_lead']);

    $date = today()->startOfMonth()->toDateString();
    $lead = CRMHelper::lead(['expected_closing_date' => $date]);

    Livewire::test(ListForecast::class)->call('moveRecord', (string) $lead->id, '2027-02', []);

    expect($lead->fresh()->expected_closing_date->toDateString())->toBe($date);
});

it('has no "Add" column', function () {
    CRMHelper::actingAs(['view_any_crm_forecast', ...CRMHelper::permissionsFor(['stage'])]);

    $component = Livewire::test(ListForecast::class)->assertDontSee('Add Stage');

    expect(method_exists($component->instance(), 'canAddKanbanColumn'))->toBeFalse();
});

it('switches the forecast between kanban, graph and list without leaving the page', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $component = Livewire::test(ListForecast::class)
        ->assertActionDoesNotExist('view_graph')
        ->assertSeeHtml('crm-layout-switcher');

    expect($component->instance()->getViewLayouts())->toBe([ViewLayout::KANBAN, ViewLayout::GRAPH, ViewLayout::LIST])
        ->and($component->instance()->getViewLayout())->toBe(ViewLayout::KANBAN)
        ->and(method_exists($component->instance(), 'getRecordNavigationTabs'))->toBeFalse();

    $component->call('switchViewLayout', 'list')->assertSet('viewLayout', 'list');

    expect($component->instance()->getTableFilterState('upcoming_closings')['isActive'] ?? null)->toBeTrue();

    $component->call('switchViewLayout', 'graph')->assertSet('viewLayout', 'graph');
});

it('applies the list toggle filters (won, lost) on the board', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $won = CRMHelper::opportunity(['stage_id' => CRMHelper::wonStage()->id, 'expected_closing_date' => now()->addMonth()->toDateString()]);
    $lost = CRMHelper::opportunity(['expected_closing_date' => now()->addMonth()->toDateString()]);
    $lost->markAsLost(null);

    $ids = fn (array $presets): array => collect(forecastColumns(Livewire::test(ListForecast::class)
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
    $user = CRMHelper::actingAs(['view_any_crm_forecast']);

    $month = forecastMonth(1);
    $closing = today()->startOfMonth()->addMonth()->addDays(5)->toDateString();

    $overdue = CRMHelper::opportunity(['expected_closing_date' => $closing]);
    $overdue->addMessage([
        'type'             => 'activity',
        'activity_type_id' => ActivityType::query()->where('plugin', 'crm')->value('id'),
        'date_deadline'    => now()->subDay()->toDateString(),
        'assigned_to'      => $user->id,
    ]);
    $quiet = CRMHelper::opportunity(['expected_closing_date' => $closing]);

    $component = Livewire::test(ListForecast::class);
    $progress = $component->instance()->kanbanColumnProgress(forecastColumn($component, $month));

    expect($progress['overdue'])->toBeGreaterThanOrEqual(1)
        ->and($progress['none'])->toBeGreaterThanOrEqual(1);

    $component->call('toggleKanbanActivityFilter', $month, 'overdue');

    expect(forecastColumn($component, $month)->records->modelKeys())
        ->toContain($overdue->id)
        ->not->toContain($quiet->id);
});

it('marks won and lost opportunities with a ribbon', function () {
    CRMHelper::actingAs(['view_any_crm_forecast']);

    $won = CRMHelper::opportunity([
        'expected_closing_date' => today()->addMonth()->toDateString(),
        'stage_id'              => Stage::query()->where('is_won', true)->value('id'),
    ]);

    Livewire::test(ListForecast::class)
        ->assertSee($won->name)
        ->assertSeeHtml('bg-success-600');
});
