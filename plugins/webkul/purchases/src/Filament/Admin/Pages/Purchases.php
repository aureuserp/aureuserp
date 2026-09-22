<?php

namespace Webkul\Purchase\Filament\Admin\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
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
use Webkul\Purchase\Filament\Admin\Widgets\VendorSpendChart;
use Webkul\Purchase\Models\Order;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;
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
                        __('purchases::filament/admin/pages/dashboard.filters.date-range'),
                        'start_date',
                        'end_date',
                    ),

                    Select::make('country_id')
                        ->label(__('purchases::filament/admin/pages/dashboard.filters.country'))
                        ->options(fn () => Country::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('purchases::filament/admin/pages/dashboard.filters.all-countries'))
                        ->live(),

                    Select::make('product_id')
                        ->label(__('purchases::filament/admin/pages/dashboard.filters.product'))
                        ->options(fn () => Product::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('purchases::filament/admin/pages/dashboard.filters.all-products'))
                        ->live(),

                    Select::make('partner_id')
                        ->label(__('purchases::filament/admin/pages/dashboard.filters.vendor'))
                        ->options(fn () => Partner::where('supplier_rank', '>', 0)->pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('purchases::filament/admin/pages/dashboard.filters.all-vendors'))
                        ->live(),

                    Select::make('category_id')
                        ->label(__('purchases::filament/admin/pages/dashboard.filters.category'))
                        ->options(fn () => Category::pluck('name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('purchases::filament/admin/pages/dashboard.filters.all-categories'))
                        ->live(),

                    Select::make('buyer_id')
                        ->label(__('purchases::filament/admin/pages/dashboard.filters.buyer'))
                        ->options(fn () => User::whereIn('id', Order::distinct()->pluck('user_id')->filter())
                            ->pluck('name', 'id')
                            ->toArray())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('purchases::filament/admin/pages/dashboard.filters.all-buyers'))
                        ->live(),

                    Select::make('state')
                        ->label(__('purchases::filament/admin/pages/dashboard.filters.state'))
                        ->options(OrderState::options())
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('purchases::filament/admin/pages/dashboard.filters.all-states'))
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
            PurchaseStatsWidget::class,
            PurchaseTrendWidget::class,
            VendorSpendChart::class,
            TopOrdersWidget::class,
            TopPurchasedProductsWidget::class,
        ];
    }
}
