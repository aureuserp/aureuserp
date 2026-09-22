<?php

namespace Webkul\Sale\Filament\Widgets;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopQuotationsTable extends TableWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 8;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordUrl(fn ($record): ?string => QuotationResource::canAccess()
                ? QuotationResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
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
            ->where('amount_total', '>', 0)
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
                ->placeholder('-')
                ->wrap(),
            Tables\Columns\TextColumn::make('state')
                ->label(__('sales::filament/widgets/sales-dashboard.status'))
                ->badge()
                ->formatStateUsing(fn ($state) => $state instanceof BackedEnum ? $state->getLabel() : $state)
                ->color(fn ($state): string => match ($state instanceof BackedEnum ? $state->value : $state) {
                    'sale'   => 'success',
                    'sent'   => 'info',
                    'cancel' => 'danger',
                    default  => 'gray',
                }),
            Tables\Columns\TextColumn::make('invoice_status')
                ->label(__('sales::filament/widgets/sales-dashboard.billing'))
                ->badge()
                ->formatStateUsing(fn ($state) => $state instanceof BackedEnum ? $state->getLabel() : $state)
                ->color(fn ($state): string => match ($state instanceof BackedEnum ? $state->value : $state) {
                    'invoiced'   => 'success',
                    'to_invoice' => 'warning',
                    default      => 'gray',
                }),
            Tables\Columns\TextColumn::make('amount_total')
                ->label(__('sales::filament/widgets/sales-dashboard.top-quotations.columns.amount'))
                ->money(fn ($record) => $record->currency?->name)
                ->alignEnd(),
        ];
    }
}
