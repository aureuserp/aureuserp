<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;

class TopPurchasedProductsWidget extends BaseWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected const BAR_COLOR = '#2a78d6';

    protected const TRACK_COLOR = 'rgba(137, 135, 129, 0.22)';

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    protected ?float $peak = null;

    public function table(Table $table): Table
    {
        return $table->defaultKeySort(false);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.top-products.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->purchaseLines()
            ->leftJoin('products_products', 'products_products.id', '=', 'purchases_order_lines.product_id')
            ->groupBy('products_products.id', 'products_products.name')
            ->selectRaw('COALESCE(products_products.id, 0) as id')
            ->addSelect('products_products.name as name')
            ->selectRaw('SUM(purchases_order_lines.product_qty) as total_quantity')
            ->selectRaw('SUM(purchases_order_lines.price_total) as total_value')
            ->havingRaw('SUM(purchases_order_lines.price_total) > 0')
            ->orderByDesc('total_value')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-products.columns.name'))
                ->placeholder(__('purchases::filament/admin/widgets/purchase-dashboard.unknown'))
                ->wrap(),
            Tables\Columns\TextColumn::make('share')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.share'))
                ->state(fn ($record): string => $this->shareBar((float) $record->total_value))
                ->html(),
            Tables\Columns\TextColumn::make('total_quantity')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-products.columns.quantity'))
                ->numeric()
                ->alignEnd(),
            Tables\Columns\TextColumn::make('total_value')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-products.columns.value'))
                ->formatStateUsing(fn ($state) => money($state ?? 0, $this->dashboardCurrency()))
                ->alignEnd(),
        ];
    }

    protected function shareBar(float $value): string
    {
        $this->peak ??= (float) ($this->getTableQuery()->get()->max('total_value') ?? 0);

        $width = $this->peak > 0 ? max(2, min(100, (int) round($value / $this->peak * 100))) : 0;

        return '<span style="display:block;width:100%;height:6px;border-radius:3px;background:'.self::TRACK_COLOR.'">'
            .'<span style="display:block;width:'.$width.'%;height:6px;border-radius:3px;background:'.self::BAR_COLOR.'"></span>'
            .'</span>';
    }
}
