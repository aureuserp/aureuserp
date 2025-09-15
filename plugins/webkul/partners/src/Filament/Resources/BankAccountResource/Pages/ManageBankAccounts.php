<?php

namespace Webkul\Partner\Filament\Resources\BankAccountResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Filament\Resources\BankAccountResource;
use Webkul\Partner\Models\BankAccount;

class ManageBankAccounts extends ManageRecords
{
    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('partners::filament/resources/bank-account/pages/manage-bank-accounts.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::id();

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('partners::filament/resources/bank-account/pages/manage-bank-accounts.header-actions.create.notification.title'))
                        ->body(__('partners::filament/resources/bank-account/pages/manage-bank-accounts.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('partners::filament/resources/bank-account/pages/manage-bank-accounts.tabs.all'))
                ->badge(BankAccount::count()),
            'archived' => Tab::make(__('partners::filament/resources/bank-account/pages/manage-bank-accounts.tabs.archived'))
                ->badge(BankAccount::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
