<?php

namespace Webkul\Support\Filament\Resources\BankResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Support\Filament\Resources\BankResource;
use Webkul\Support\Models\Bank;

class ManageBanks extends ManageRecords
{
    protected static string $resource = BankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('support::filament/resources/bank/pages/manage-banks.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/bank/pages/manage-banks.header-actions.create.notification.title'))
                        ->body(__('support::filament/resources/bank/pages/manage-banks.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('support::filament/resources/bank/pages/manage-banks.tabs.all'))
                ->badge(Bank::count()),
            'archived' => Tab::make(__('support::filament/resources/bank/pages/manage-banks.tabs.archived'))
                ->badge(Bank::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
