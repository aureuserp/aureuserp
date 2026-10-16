<?php

namespace Webkul\Invoice\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Enums\PaymentState;
use Webkul\Invoice\Filament\Widgets;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;

class Invoices extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'invoice';

    protected static function getPagePermission(): ?string
    {
        return 'page_invoice_invoices';
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/pages/dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function filtersForm(Schema $form): Schema
    {
        return $form->schema([
            Section::make()
                ->schema([
                    ...DashboardDateRange::make(
                        __('invoices::filament/pages/dashboard.filters.date-range'),
                        'start_date',
                        'end_date',
                    ),

                    Select::make('product_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.product'))
                        ->options(fn () => Product::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-products'))
                        ->live(),

                    Select::make('category_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.category'))
                        ->options(fn () => Category::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-categories'))
                        ->live(),

                    Select::make('customer_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.customer'))
                        ->options(fn () => Partner::where('customer_rank', '>', 0)->pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-customers'))
                        ->live(),

                    Select::make('vendor_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.vendor'))
                        ->options(fn () => Partner::where('supplier_rank', '>', 0)->pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-vendors'))
                        ->live(),

                    Select::make('salesperson_id')
                        ->label(__('invoices::filament/pages/dashboard.filters.salesperson'))
                        ->options(fn () => User::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-salespersons'))
                        ->live(),

                    Select::make('payment_state')
                        ->label(__('invoices::filament/pages/dashboard.filters.payment-state'))
                        ->options(PaymentState::options())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('invoices::filament/pages/dashboard.filters.all-payment-states'))
                        ->live(),

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

    public function getWidgets(): array
    {
        return [
            Widgets\InvoiceStatsWidget::class,
            Widgets\BillStatsWidget::class,
            Widgets\RevenueOverTimeWidget::class,
            Widgets\CustomerRevenueChart::class,
            Widgets\SalespersonPerformanceChart::class,
            Widgets\TopInvoicesWidget::class,
            Widgets\TopBillsWidget::class,
        ];
    }
}
