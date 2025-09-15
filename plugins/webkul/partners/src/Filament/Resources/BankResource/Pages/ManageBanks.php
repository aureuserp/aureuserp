<?php

namespace Webkul\Partner\Filament\Resources\BankResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Filament\Resources\BankResource;
use Webkul\Support\Models\Bank;

class ManageBanks extends ManageRecords
{
    protected static string $resource = BankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('partners::filament/resources/bank/pages/manage-banks.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::id();

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('partners::filament/resources/bank/pages/manage-banks.header-actions.create.notification.title'))
                        ->body(__('partners::filament/resources/bank/pages/manage-banks.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('partners::filament/resources/bank/pages/manage-banks.tabs.all'))
                ->badge(Bank::count()),
            'archived' => Tab::make(__('partners::filament/resources/bank/pages/manage-banks.tabs.archived'))
                ->badge(Bank::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
