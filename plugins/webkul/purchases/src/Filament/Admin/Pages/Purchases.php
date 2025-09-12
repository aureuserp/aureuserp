<?php

namespace Webkul\Purchase\Filament\Admin\Pages;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseStatsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopOrdersWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopPurchasedProductsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopVendorsWidget;
use Webkul\Support\Filament\Clusters\Dashboard as DashboardCluster;

class Purchases extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static string $routePath = 'purchase';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $cluster = DashboardCluster::class;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/pages/dashboard.navigation.title');
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    Select::make('date_range')
                        ->label('Date Range')
                        ->options([
                            'week'     => 'Last 7 days',
                            'month'    => 'Last 30 days',
                            '3_months' => 'Last 3 months',
                            '6_months' => 'Last 6 months',
                            'year'     => 'Last 1 year',
                            '3_years'  => 'Last 3 years',
                        ])
                        ->default('month')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $endDate = Carbon::now()->format('Y-m-d');
                            switch ($state) {
                                case 'week':
                                    $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
                                    break;
                                case 'month':
                                    $startDate = Carbon::now()->subMonth()->format('Y-m-d');
                                    break;
                                case '3_months':
                                    $startDate = Carbon::now()->subMonths(3)->format('Y-m-d');
                                    break;
                                case '6_months':
                                    $startDate = Carbon::now()->subMonths(6)->format('Y-m-d');
                                    break;
                                case 'year':
                                    $startDate = Carbon::now()->subYear()->format('Y-m-d');
                                    break;
                                case '3_years':
                                    $startDate = Carbon::now()->subYears(3)->format('Y-m-d');
                                    break;
                                default:
                                    $startDate = Carbon::now()->subMonth()->format('Y-m-d');
                                    break;
                            }

                            $set('start_date', $startDate);
                            $set('end_date', $endDate);
                        }),

                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->maxDate(fn (Get $get) => $get('end_date') ?: now())
                        ->default(fn (Get $get) => $get('start_date') ?: now()->subMonth()->format('Y-m-d'))
                        ->reactive()
                        ->hidden()
                        ->native(false),

                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->minDate(fn (Get $get) => $get('start_date') ?: now())
                        ->maxDate(now())
                        ->default(fn (Get $get) => $get('end_date') ?: now()->format('Y-m-d'))
                        ->reactive()
                        ->hidden()
                        ->native(false),
                    Select::make('product_id')
                        ->label('Product')
                        ->options(fn () => Product::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('All Products')
                        ->reactive(),

                    Select::make('partner_id')
                        ->label('Vendor')
                        ->options(fn () => Partner::where('sub_type', 'supplier')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('All Vendors')
                        ->reactive(),

                ])
                ->columns(3),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            PurchaseStatsWidget::class,
            TopOrdersWidget::class,
            TopVendorsWidget::class,
            TopPurchasedProductsWidget::class,

        ];
    }
}
