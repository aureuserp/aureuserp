<?php

namespace Webkul\Account\Filament\Resources\AccountTagResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Resources\AccountTagResource;

class ListAccountTags extends ListRecords
{
    protected static string $resource = AccountTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::user()->id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/account-tag/pages/list-account-tag.header-actions.notification.title'))
                        ->body(__('accounts::filament/resources/account-tag/pages/list-account-tag.header-actions.notification.body'))
                ),
        ];
    }
}
