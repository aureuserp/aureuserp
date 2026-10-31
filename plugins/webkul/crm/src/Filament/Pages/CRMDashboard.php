<?php

namespace Webkul\Crm\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Filament\Widgets\CRMStatsOverview;
use Webkul\Crm\Filament\Widgets\LeadsBySourceChart;
use Webkul\Crm\Filament\Widgets\PipelineByStageChart;
use Webkul\Crm\Filament\Widgets\SalespersonWonRevenueChart;
use Webkul\Crm\Filament\Widgets\TopOpportunitiesTable;
use Webkul\Crm\Filament\Widgets\WonRevenueChart;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;

class CRMDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'crm';

    protected static ?int $navigationSort = 100;

    protected static function getPagePermission(): ?string
    {
        return 'page_crm_c_r_m_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/pages/crm-dashboard.navigation.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('crm::filament/pages/crm-dashboard.title');
    }

    public static function getNavigationGroup(): string|UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        ...DashboardDateRange::make(__('crm::filament/pages/crm-dashboard.filters.date-range')),
                        Select::make('teams')
                            ->label(__('crm::filament/pages/crm-dashboard.filters.teams'))
                            ->placeholder(__('crm::filament/pages/crm-dashboard.filters.all-teams'))
                            ->multiple()
                            ->searchable()
                            ->options(fn (): array => Report::getTeamOptions())
                            ->live(),
                        Select::make('salesPersons')
                            ->label(__('crm::filament/pages/crm-dashboard.filters.salespeople'))
                            ->placeholder(__('crm::filament/pages/crm-dashboard.filters.all-salespeople'))
                            ->multiple()
                            ->searchable()
                            ->options(fn (): array => Report::getSalespersonOptions())
                            ->live(),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'xl'      => 3,
                    ]),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            CRMStatsOverview::class,
            WonRevenueChart::class,
            PipelineByStageChart::class,
            LeadsBySourceChart::class,
            SalespersonWonRevenueChart::class,
            TopOpportunitiesTable::class,
        ];
    }
}
