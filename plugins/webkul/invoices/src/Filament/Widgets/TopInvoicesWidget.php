<?php

namespace Webkul\Invoice\Filament\Widgets;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

class TopInvoicesWidget extends BaseWidget
{
    use HasInvoiceDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 6;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table->defaultKeySort(false);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.top-invoices.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->customerInvoices()
            ->with(['partner', 'invoiceUser', 'currency'])
            ->where('amount_total', '>', 0)
            ->orderByDesc('amount_total')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.reference')),
            Tables\Columns\TextColumn::make('partner.name')
                ->label(__('invoices::filament/widgets/invoice-dashboard.customer'))
                ->placeholder('-')
                ->wrap(),
            Tables\Columns\TextColumn::make('invoice_date')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.date'))
                ->date(),
            Tables\Columns\TextColumn::make('payment_state')
                ->label(__('invoices::filament/widgets/invoice-dashboard.payment-state'))
                ->badge()
                ->formatStateUsing(fn ($state) => $state instanceof BackedEnum ? $state->getLabel() : $state)
                ->color(fn ($state): string => match ($state instanceof BackedEnum ? $state->value : $state) {
                    'paid'           => 'success',
                    'partial'        => 'warning',
                    'not_paid'       => 'danger',
                    'reversed'       => 'info',
                    default          => 'gray',
                }),
            Tables\Columns\TextColumn::make('amount_total')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.amount'))
                ->money(fn ($record) => $record->currency?->name ?? $this->dashboardCurrency())
                ->alignEnd(),
        ];
    }
}
