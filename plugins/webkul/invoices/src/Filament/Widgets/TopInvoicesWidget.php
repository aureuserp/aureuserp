<?php

namespace Webkul\Invoice\Filament\Widgets;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

class TopInvoicesWidget extends BaseWidget
{
    use HasInvoiceDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 7;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordClasses('dashboard-table-row')
            ->recordUrl(fn ($record): ?string => $record && InvoiceResource::canAccess()
                ? InvoiceResource::getUrl('view', ['record' => $record])
                : null);
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
            ->orderByDesc('amount_total_signed')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.reference'))
                ->weight(FontWeight::Bold)
                ->color('primary')
                ->description(fn ($record): string => $record->partner?->name ?: '—'),

            TextColumn::make('invoice_date')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.date'))
                ->date()
                ->color('gray'),

            TextColumn::make('payment_state')
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

            TextColumn::make('amount_total')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.amount'))
                ->money(fn ($record) => $record->currency?->name ?? $this->dashboardCurrency())
                ->weight(FontWeight::Bold)
                ->alignEnd(),
        ];
    }
}
