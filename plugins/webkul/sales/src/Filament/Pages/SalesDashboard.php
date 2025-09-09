<?php

namespace Webkul\Sale\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\View\LegacyComponents\Widget;
use Webkul\Sale\Models\Order;
use Webkul\Support\Filament\Clusters\Dashboard as DashboardCluster;

class SalesDashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'sale';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $cluster = DashboardCluster::class;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/pages/sales-dashboard.navigation.title');
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.start-date'))
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->default(now()->subMonth())
                            ->native(false),

                        DatePicker::make('end_date')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.end-date'))
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now())
                            ->default(now()) // ✅ Default = today
                            ->native(false),
                        Select::make('salesperson_id')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.salesperson'))
                            ->options(
                                fn () => Order::get()
                                    ->pluck('user.name', 'id')
                                    ->toArray()
                            )
                            ->placeholder('All Salespersons'),
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
            \Webkul\Sale\Filament\Widgets\StatsOverviewWidget::class,
            \Webkul\Sale\Filament\Widgets\SalesChartWidget::class,
            \Webkul\Sale\Filament\Widgets\YearlyComparisonWidget::class,
            \Webkul\Sale\Filament\Widgets\TopSalesOrdersWidget::class,
            \Webkul\Sale\Filament\Widgets\TopProductsWidget::class,
            \Webkul\Sale\Filament\Widgets\TopCategoriesWidget::class,
        ];
    }
}
