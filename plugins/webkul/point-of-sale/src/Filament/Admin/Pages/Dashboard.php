<?php

namespace Webkul\PointOfSale\Filament\Admin\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;
use Webkul\Partner\Models\Partner;
use Webkul\PointOfSale\Filament\Admin\Widgets\SessionStatsWidget;
use Webkul\PointOfSale\Filament\Admin\Widgets\TopProductsWidget;
use Webkul\PointOfSale\Models\Category;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'point-of-sale';

    protected static ?string $slug = 'point-of-sale-dashboard';

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/pages/dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string|UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/pages/dashboard.title');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'md'      => 3,
                        'xl'      => 6,
                    ])
                    ->schema([
                        Select::make('selectedTerminals')
                            ->label(__('point-of-sale::filament/admin/pages/dashboard.filters-form.terminals'))
                            ->placeholder(__('point-of-sale::filament/admin/pages/dashboard.filters-form-placeholders.terminals'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->options(fn (): array => Config::query()->pluck('name', 'id')->all())
                            ->live(),
                        ...DashboardDateRange::make(
                            __('point-of-sale::filament/admin/pages/dashboard.filters-form.date-range'),
                        ),
                        Select::make('selectedProducts')
                            ->label(__('point-of-sale::filament/admin/pages/dashboard.filters-form.products'))
                            ->placeholder(__('point-of-sale::filament/admin/pages/dashboard.filters-form-placeholders.products'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->options(fn (): array => Product::query()
                                ->where('available_in_pos', true)
                                ->orderBy('name')
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getSearchResultsUsing(fn (string $search): array => Product::query()
                                ->where('available_in_pos', true)
                                ->where('name', 'like', "%{$search}%")
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelsUsing(fn (array $values): array => Product::withTrashed()
                                ->whereIn('id', $values)
                                ->pluck('name', 'id')
                                ->all())
                            ->live(),
                        Select::make('selectedCategories')
                            ->label(__('point-of-sale::filament/admin/pages/dashboard.filters-form.categories'))
                            ->placeholder(__('point-of-sale::filament/admin/pages/dashboard.filters-form-placeholders.categories'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->options(fn (): array => Category::query()->pluck('name', 'id')->all())
                            ->live(),
                        Select::make('selectedSalespersons')
                            ->label(__('point-of-sale::filament/admin/pages/dashboard.filters-form.salespersons'))
                            ->placeholder(__('point-of-sale::filament/admin/pages/dashboard.filters-form-placeholders.salespersons'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->options(fn (): array => User::query()->pluck('name', 'id')->all())
                            ->live(),
                        Select::make('selectedCustomers')
                            ->label(__('point-of-sale::filament/admin/pages/dashboard.filters-form.customers'))
                            ->placeholder(__('point-of-sale::filament/admin/pages/dashboard.filters-form-placeholders.customers'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->options(fn (): array => Partner::query()
                                ->orderBy('name')
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getSearchResultsUsing(fn (string $search): array => Partner::query()
                                ->where('name', 'like', "%{$search}%")
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelsUsing(fn (array $values): array => Partner::withTrashed()
                                ->whereIn('id', $values)
                                ->pluck('name', 'id')
                                ->all())
                            ->live(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            SessionStatsWidget::class,
            TopProductsWidget::class,
        ];
    }
}
