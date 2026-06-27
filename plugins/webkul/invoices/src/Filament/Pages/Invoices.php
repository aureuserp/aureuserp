<?php

namespace Webkul\Invoice\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Enums\PaymentState;
use Webkul\Invoice\Filament\Widgets;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

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

    public static function getNavigationGroup(): string
    {
        return __('projects::filament/pages/dashboard.navigation.group');
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
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->maxDate(fn (Get $get) => $get('end_date') ?: now())
                        ->default(now()->subMonth())
                        ->native(false),

                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->minDate(fn (Get $get) => $get('start_date') ?: now())
                        ->maxDate(now())
                        ->default(now())
                        ->native(false),

                    Select::make('product_id')
                        ->label('Product')
                        ->options(fn () => Product::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Products')
                        ->live(),

                    Select::make('category_id')
                        ->label('Category')
                        ->options(fn () => Category::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Categories')
                        ->live(),

                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(fn () => Partner::where('customer_rank', '>', 0)->pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Customers')
                        ->live(),

                    Select::make('vendor_id')
                        ->label('Vendor')
                        ->options(fn () => Partner::where('supplier_rank', '>', 0)->pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Vendors')
                        ->live(),

                    Select::make('salesperson_id')
                        ->label('Salesperson')
                        ->options(fn () => User::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Salespersons')
                        ->live(),

                    Select::make('payment_state')
                        ->label('Payment Status')
                        ->options(PaymentState::options())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Payment States')
                        ->live(),

                ])
                ->columns(4)
                ->columnSpanFull(),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            Widgets\InvoiceStatsWidget::make(),
            Widgets\BillStatsWidget::make(),
            Widgets\RevenueOverTimeWidget::make(),
            Widgets\TopInvoicesWidget::make(),
            Widgets\TopBillsWidget::make(),
            Widgets\TopCustomersWidget::make(),
            Widgets\TopSalespersonsWidget::make(),
        ];
    }
}
