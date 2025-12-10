<?php

namespace Webkul\Account\Filament\Resources\AccountResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Filament\Resources\AccountResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageAccounts extends ManageRecords
{
    use HasTableViews;

    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/account/pages/manage-accounts.header-actions.create.notification.title'))
                        ->body(__('accounts::filament/resources/account/pages/manage-accounts.header-actions.create.notification.body'))
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'receivable' => PresetView::make(__('accounts::filament/resources/account/pages/manage-accounts.tabs.receivable'))
                ->favorite()
                ->icon('heroicon-o-arrow-down-tray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::ASSET_RECEIVABLE)),
            'payable' => PresetView::make(__('accounts::filament/resources/account/pages/manage-accounts.tabs.payable'))
                ->favorite()
                ->icon('heroicon-o-arrow-up-tray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::LIABILITY_PAYABLE)),
            'equity' => PresetView::make(__('accounts::filament/resources/account/pages/manage-accounts.tabs.equity'))
                ->favorite()
                ->icon('heroicon-o-scale')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('account_type', [
                    AccountType::EQUITY,
                    AccountType::EQUITY_UNAFFECTED,
                ])),
            'assets' => PresetView::make(__('accounts::filament/resources/account/pages/manage-accounts.tabs.assets'))
                ->favorite()
                ->icon('heroicon-o-building-library')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('account_type', [
                    AccountType::ASSET_RECEIVABLE,
                    AccountType::ASSET_CASH,
                    AccountType::ASSET_CURRENT,
                    AccountType::ASSET_NON_CURRENT,
                    AccountType::ASSET_PREPAYMENTS,
                    AccountType::ASSET_FIXED,
                ])),
            'liability' => PresetView::make(__('accounts::filament/resources/account/pages/manage-accounts.tabs.liability'))
                ->favorite()
                ->icon('heroicon-o-credit-card')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('account_type', [
                    AccountType::LIABILITY_PAYABLE,
                    AccountType::LIABILITY_CREDIT_CARD,
                    AccountType::LIABILITY_CURRENT,
                    AccountType::LIABILITY_NON_CURRENT,
                ])),
            'income' => PresetView::make(__('accounts::filament/resources/account/pages/manage-accounts.tabs.income'))
                ->favorite()
                ->icon('heroicon-o-arrow-trending-up')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('account_type', [
                    AccountType::INCOME,
                    AccountType::INCOME_OTHER,
                ])),
            'expenses' => PresetView::make(__('accounts::filament/resources/account/pages/manage-accounts.tabs.expenses'))
                ->favorite()
                ->icon('heroicon-o-arrow-trending-down')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('account_type', [
                    AccountType::EXPENSE,
                    AccountType::EXPENSE_DEPRECIATION,
                    AccountType::EXPENSE_DIRECT_COST,
                ])),
        ];
    }
}
