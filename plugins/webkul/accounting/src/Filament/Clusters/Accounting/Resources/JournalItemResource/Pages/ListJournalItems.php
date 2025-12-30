<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalItemResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalItemResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListJournalItems extends ListRecords
{
    use HasTableViews;

    protected static string $resource = JournalItemResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'unposted' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.unposted'))
                ->favorite()
                ->icon('heroicon-s-stop')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_state', MoveState::DRAFT)),
            'posted' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.posted'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_state', MoveState::POSTED)),
            'to_check' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.to-check'))
                ->icon('heroicon-s-check-badge')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('move', fn ($q) => $q->where('checked', false))),
            'unreconciled' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.unreconciled'))
                ->icon('heroicon-s-arrows-right-left')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('reconciled', false)),
            'with_residual' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.with-residual'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('amount_residual', '!=', 0)),
            'sales' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.sales'))
                ->icon('heroicon-s-shopping-cart')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', fn ($q) => $q->where('type', JournalType::SALE))),
            'purchases' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.purchases'))
                ->icon('heroicon-s-shopping-bag')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', fn ($q) => $q->where('type', JournalType::PURCHASE))),
            'bank' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.bank'))
                ->icon('heroicon-s-building-library')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', fn ($q) => $q->where('type', JournalType::BANK))),
            'cash' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.cash'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', fn ($q) => $q->where('type', JournalType::CASH))),
            'credit' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.credit'))
                ->icon('heroicon-s-credit-card')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', fn ($q) => $q->where('type', JournalType::CREDIT_CARD))),
            'miscellaneous' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.miscellaneous'))
                ->icon('heroicon-s-ellipsis-horizontal-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', fn ($q) => $q->where('type', JournalType::GENERAL))),
            'payable' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.payable'))
                ->icon('heroicon-s-arrow-trending-down')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('account', fn ($q) => $q->where('account_type', AccountType::LIABILITY_PAYABLE))),
            'receivable' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.receivable'))
                ->icon('heroicon-s-arrow-trending-up')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('account', fn ($q) => $q->where('account_type', AccountType::ASSET_RECEIVABLE))),
            'pl_accounts' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-item.table.saved-filters.pl-accounts'))
                ->icon('heroicon-s-chart-bar')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('account', function ($q) {
                        $q->whereIn('account_type', [
                            AccountType::INCOME,
                            AccountType::INCOME_OTHER,
                            AccountType::EXPENSE,
                            AccountType::EXPENSE_DEPRECIATION,
                            AccountType::EXPENSE_DIRECT_COST,
                        ]);
                    });
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
