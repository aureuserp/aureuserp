<?php

namespace Webkul\Project\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\View\LegacyComponents\Widget;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Filament\Widgets;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Tag;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Clusters\Dashboard as DashboardCluster;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static string $routePath = 'project';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $cluster = DashboardCluster::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/pages/dashboard.navigation.title');
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('selectedProjects')
                            ->label(__('projects::filament/pages/dashboard.filters-form.project'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(Project::all()->pluck('name', 'id')) // Modified here
                            ->reactive(),
                        Select::make('selectedAssignees')
                            ->label(__('projects::filament/pages/dashboard.filters-form.assignees'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(User::all()->pluck('name', 'id')) // Modified here
                            ->reactive(),
                        Select::make('selectedTags')
                            ->label(__('projects::filament/pages/dashboard.filters-form.tags'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(Tag::all()->pluck('name', 'id')) // Modified here
                            ->reactive(),
                        Select::make('selectedPartners')
                            ->label(__('projects::filament/pages/dashboard.filters-form.customer'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(Partner::all()->pluck('name', 'id')) // Modified here
                            ->reactive(),
                        DatePicker::make('startDate')
                            ->label(__('projects::filament/pages/dashboard.filters-form.start-date'))
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->default(now()->subMonth()->format('Y-m-d'))
                            ->native(false),
                        DatePicker::make('endDate')
                            ->label(__('projects::filament/pages/dashboard.filters-form.end-date'))
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now())
                            ->default(now())
                            ->native(false),
                    ])
                    ->columns(3),
            ]);
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            Widgets\StatsOverviewWidget::class,
            Widgets\TaskByStageChart::class,
            Widgets\TaskByStateChart::class,
            Widgets\TopAssigneesWidget::class,
            Widgets\TopProjectsWidget::class,
        ];
    }
}
