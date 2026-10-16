<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopProductsTable extends TableWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected const BAR_COLOR = '#2a78d6';

    protected const TRACK_COLOR = 'rgba(137, 135, 129, 0.22)';

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    protected ?float $peak = null;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordUrl(fn ($record): ?string => $record->id && ProductResource::canAccess()
                ? ProductResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.top-products.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->confirmedLines()
            ->leftJoin('products_products', 'products_products.id', '=', 'sales_order_lines.product_id')
            ->groupBy('products_products.id', 'products_products.name')
            ->selectRaw('COALESCE(products_products.id, 0) as id')
            ->addSelect('products_products.name as name')
            ->selectRaw('SUM(sales_order_lines.product_uom_qty) as quantity')
            ->selectRaw('SUM(sales_order_lines.price_total) as revenue')
            ->havingRaw('SUM(sales_order_lines.price_total) > 0')
            ->orderByDesc('revenue')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.name'))
                ->placeholder(__('sales::filament/widgets/sales-dashboard.unknown'))
                ->wrap(),
            Tables\Columns\TextColumn::make('share')
                ->label(__('sales::filament/widgets/sales-dashboard.share'))
                ->state(fn ($record): string => $this->shareBar((float) $record->revenue))
                ->html(),
            Tables\Columns\TextColumn::make('quantity')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.quantity'))
                ->numeric()
                ->alignEnd(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0, current_company()?->currency?->name))
                ->alignEnd(),
        ];
    }

    protected function shareBar(float $revenue): string
    {
        $this->peak ??= (float) ($this->getTableQuery()->get()->max('revenue') ?? 0);

        $width = $this->peak > 0 ? max(2, min(100, (int) round($revenue / $this->peak * 100))) : 0;

        return '<span style="display:block;width:100%;height:6px;border-radius:3px;background:'.self::TRACK_COLOR.'">'
            .'<span style="display:block;width:'.$width.'%;height:6px;border-radius:3px;background:'.self::BAR_COLOR.'"></span>'
            .'</span>';
    }
}
