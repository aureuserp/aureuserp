<?php

namespace Webkul\Recruitment\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\View\LegacyComponents\Widget;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\EmployeeJobPosition;
use Webkul\Recruitment\Filament\Widgets\ApplicantChartWidget;
use Webkul\Recruitment\Filament\Widgets\JobPositionStatsWidget;
use Webkul\Recruitment\Models\Stage;
use Webkul\Support\Filament\Clusters\Dashboard as DashboardCluster;
use Webkul\Support\Models\Company;

class Recruitments extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static string $routePath = 'recruitment';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $cluster = DashboardCluster::class;

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/pages/recruitment.navigation.title');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('selectedJobs')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.job-position'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => EmployeeJobPosition::where('is_active', true)->pluck('name', 'id'))
                            ->reactive(),
                        Select::make('selectedDepartments')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.departments'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Department::pluck('name', 'id'))
                            ->reactive(),
                        Select::make('selectedCompanies')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.companies'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Company::pluck('name', 'id'))
                            ->reactive(),
                        Select::make('selectedStages')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.stages'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Stage::pluck('name', 'id'))
                            ->reactive(),
                        Select::make('status')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.status.title'))
                            ->options([
                                'all'      => __('recruitments::filament/pages/recruitment.filters-form.status.options.all'),
                                'ongoing'  => __('recruitments::filament/pages/recruitment.filters-form.status.options.ongoing'),
                                'hired'    => __('recruitments::filament/pages/recruitment.filters-form.status.options.hired'),
                                'refused'  => __('recruitments::filament/pages/recruitment.filters-form.status.options.refused'),
                                'archived' => __('recruitments::filament/pages/recruitment.filters-form.status.options.archived'),
                            ])
                            ->default('all')
                            ->reactive(),
                        DatePicker::make('startDate')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.start-date'))
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->default(now()->subMonth()->format('Y-m-d'))
                            ->native(false),
                        DatePicker::make('endDate')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.end-date'))
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now())
                            ->default(now())
                            ->native(false),
                    ])
                    ->columns(3)->columnSpanFull(),
            ]);
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            JobPositionStatsWidget::class,
            ApplicantChartWidget::class,
        ];
    }
}
