<?php

namespace Webkul\Sale\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Product\Models\Category;
use Webkul\Sale\Filament\Widgets\MonthlySalesChart;
use Webkul\Sale\Filament\Widgets\SaleStatsOverview;
use Webkul\Sale\Filament\Widgets\TopCategoriesTable;
use Webkul\Sale\Filament\Widgets\TopCountriesTable;
use Webkul\Sale\Filament\Widgets\TopCustomersTable;
use Webkul\Sale\Filament\Widgets\TopProductsTable;
use Webkul\Sale\Filament\Widgets\TopQuotationsTable;
use Webkul\Sale\Filament\Widgets\TopSalesOrdersTable;
use Webkul\Sale\Filament\Widgets\TopSalesPersonsTable;
use Webkul\Sale\Filament\Widgets\TopSalesTeamsTable;
use Webkul\Sale\Models\Product;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

class SalesDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'sales';

    protected static function getPagePermission(): ?string
    {
        return 'page_sale_sales_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/pages/sales-dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/pages/sales-dashboard.navigation.group');
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
                        DatePicker::make('startDate')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.start-date'))
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->default(now()->subYear()->startOfMonth())
                            ->native(false),
                        DatePicker::make('endDate')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.end-date'))
                            ->minDate(fn (Get $get) => $get('startDate') ?: null)
                            ->maxDate(now())
                            ->default(now())
                            ->native(false),
                        Select::make('countries')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.countries'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Country::pluck('name', 'id')),
                        Select::make('products')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.products'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Product::pluck('name', 'id')),
                        Select::make('categories')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.categories'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Category::pluck('name', 'id')),
                        Select::make('teams')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.teams'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => Team::pluck('name', 'id')),
                        Select::make('salesPersons')
                            ->label(__('sales::filament/pages/sales-dashboard.filters.sales-persons'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => User::pluck('name', 'id')),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'md'      => 3,
                        'xl'      => 7,
                    ]),
            ]);
    }

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return [
            SaleStatsOverview::class,
            MonthlySalesChart::class,
            TopQuotationsTable::class,
            TopSalesOrdersTable::class,
            TopCountriesTable::class,
            TopProductsTable::class,
            TopCustomersTable::class,
            TopCategoriesTable::class,
            TopSalesTeamsTable::class,
            TopSalesPersonsTable::class,
        ];
    }
}
