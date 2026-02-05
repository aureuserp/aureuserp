<?php

namespace Webkul\Invoice\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\Invoice\Filament\Widgets;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Clusters\Dashboard as DashboardCluster;

class Invoices extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static string $routePath = 'invoice';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $cluster = DashboardCluster::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/pages/dashboard.navigation.title');
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

                    Select::make('salesperson_id')
                        ->label('Salesperson')
                        ->options(fn () => User::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('All Salespersons')
                        ->reactive(),

                ])
                ->columns(3),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            Widgets\InvoiceStatsWidget::make(),
            Widgets\RevenueOverTimeWidget::make(),
            Widgets\TopInvoicesWidget::make(),
            Widgets\TopCustomersWidget::make(),
            Widgets\TopSalespersonsWidget::make(),
        ];
    }
}
