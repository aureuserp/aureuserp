<?php

namespace Webkul\Sale\Filament\Pages;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\View\LegacyComponents\Widget;
use Webkul\Sale\Filament\Widgets\RevenewChartWidget;
use Webkul\Sale\Filament\Widgets\SalesChartWidget;
use Webkul\Sale\Filament\Widgets\StatsOverviewWidget;
use Webkul\Sale\Filament\Widgets\TopCategoriesWidget;
use Webkul\Sale\Filament\Widgets\TopCustomerWidget;
use Webkul\Sale\Filament\Widgets\TopProductsWidget;
use Webkul\Sale\Filament\Widgets\TopSalesCountryWidget;
use Webkul\Sale\Filament\Widgets\TopSalesTeamWidget;
use Webkul\Sale\Filament\Widgets\YearlyComparisonWidget;
use Webkul\Sale\Models\Category;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Partner;
use Webkul\Sale\Models\Product;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\Country;

class SalesDashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'sale';

    public static function getNavigationIcon(): ?string
    {
        return null;
    }

    public static function getNavigationGroup(): string
    {
        return 'Dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/pages/sales-dashboard.navigation.title');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.start-date'))
                            ->maxDate(fn (Get $get) => $get('end_date') ?: now())
                            ->default(now()->subMonth())
                            ->native(false),

                        DatePicker::make('end_date')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.end-date'))
                            ->minDate(fn (Get $get) => $get('start_date') ?: now())
                            ->maxDate(now())
                            ->default(now())
                            ->native(false),

                        Select::make('country_id')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.country'))
                            ->options(fn () => Country::pluck('name', 'id')->toArray())
                            ->multiple()
                            ->searchable()
                            ->placeholder(__('sales::filament/pages/sales-dashboard.filters-form.country')),

                        Select::make('product_id')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.product'))
                            ->options(fn () => Product::pluck('name', 'id')->toArray())
                            ->multiple()
                            ->searchable()
                            ->placeholder(__('sales::filament/pages/sales-dashboard.filters-form.product')),

                        Select::make('customer_id')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.customer'))
                            ->options(fn () => Partner::pluck('name', 'id')->toArray())
                            ->multiple()
                            ->searchable()
                            ->placeholder(__('sales::filament/pages/sales-dashboard.filters-form.customer')),

                        Select::make('category_id')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.category'))
                            ->options(fn () => Category::pluck('name', 'id')->toArray())
                            ->multiple()
                            ->searchable()
                            ->placeholder(__('sales::filament/pages/sales-dashboard.filters-form.category')),

                        Select::make('salesteam_id')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.salesteam'))
                            ->options(fn () => Team::pluck('name', 'id')->toArray())
                            ->multiple()
                            ->searchable()
                            ->placeholder(__('sales::filament/pages/sales-dashboard.filters-form.salesteam')),

                        Select::make('salesperson_id')
                            ->label(__('sales::filament/pages/sales-dashboard.filters-form.salesperson'))
                            ->options(fn () => User::whereIn('id', Order::distinct()->pluck('user_id')->filter())
                                ->pluck('name', 'id')
                                ->toArray()
                            )
                            ->multiple()
                            ->searchable()
                            ->placeholder(__('sales::filament/pages/sales-dashboard.filters-form.salesperson')),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            SalesChartWidget::class,
            RevenewChartWidget::class,
            YearlyComparisonWidget::class,
            TopCategoriesWidget::class,
            TopCustomerWidget::class,
            TopProductsWidget::class,
            TopSalesTeamWidget::class,
            TopSalesCountryWidget::class,
        ];
    }
}
