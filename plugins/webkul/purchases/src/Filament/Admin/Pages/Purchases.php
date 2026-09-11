<?php

namespace Webkul\Purchase\Filament\Admin\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseStatsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseTrendWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopOrdersWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopPurchasedProductsWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopVendorsWidget;
use Webkul\Purchase\Models\Order;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

class Purchases extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'purchase';

    protected static function getPagePermission(): ?string
    {
        return 'page_purchase_purchases';
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/pages/dashboard.navigation.title');
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

                    Select::make('country_id')
                        ->label('Country')
                        ->options(fn () => Country::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Countries')
                        ->live(),

                    Select::make('product_id')
                        ->label('Product')
                        ->options(fn () => Product::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Products')
                        ->live(),

                    Select::make('partner_id')
                        ->label('Vendor')
                        ->options(fn () => Partner::where('supplier_rank', '>', 0)->pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Vendors')
                        ->live(),

                    Select::make('category_id')
                        ->label('Category')
                        ->options(fn () => Category::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Categories')
                        ->live(),

                    Select::make('buyer_id')
                        ->label('Buyer')
                        ->options(fn () => User::whereIn('id', Order::distinct()->pluck('user_id')->filter())
                            ->pluck('name', 'id')
                            ->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Buyers')
                        ->live(),

                    Select::make('state')
                        ->label('Order State')
                        ->options(OrderState::options())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All States')
                        ->live(),

                ])
                ->columns(4)
                ->columnSpanFull(),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            PurchaseStatsWidget::class,
            PurchaseTrendWidget::class,
            TopOrdersWidget::class,
            TopVendorsWidget::class,
            TopPurchasedProductsWidget::class,

        ];
    }
}
