<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopQuotationsTable extends TableWidget
{
    use HasSaleDashboardFilters;

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table->recordUrl(fn ($record): ?string => QuotationResource::canAccess()
            ? QuotationResource::getUrl('view', ['record' => $record->id])
            : null);
    }

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.top-quotations.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->quotations()
            ->with(['partner', 'currency'])
            ->orderByDesc('amount_total')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-quotations.columns.reference')),
            Tables\Columns\TextColumn::make('partner.name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-quotations.columns.customer'))
                ->placeholder('-'),
            Tables\Columns\TextColumn::make('amount_total')
                ->label(__('sales::filament/widgets/sales-dashboard.top-quotations.columns.amount'))
                ->money(fn ($record) => $record->currency?->name),
        ];
    }
}
