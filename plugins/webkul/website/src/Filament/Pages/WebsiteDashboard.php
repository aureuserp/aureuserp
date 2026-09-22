<?php

namespace Webkul\Website\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\View\LegacyComponents\Widget;
use Webkul\Support\Filament\Clusters\Dashboard as DashboardCluster;
use Webkul\Website\Filament\Widget\StatsOverviewWidget;

class WebsiteDashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'blog';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $cluster = DashboardCluster::class;

    public static function getNavigationLabel(): string
    {
        return __('website::filament/widgets/dashboard.navigation.title');
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label(__('projects::filament/pages/dashboard.filters-form.start-date'))
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->default(now()->subMonth())
                            ->native(false),

                        DatePicker::make('endDate')
                            ->label(__('projects::filament/pages/dashboard.filters-form.end-date'))
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now())
                            ->default(now()) // ✅ Default = today
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }
}
