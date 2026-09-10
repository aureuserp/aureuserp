<?php

namespace Webkul\Support\Filament\Resources\SequenceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Support\Filament\Resources\SequenceResource;

class ManageSequences extends ManageRecords
{
    protected static string $resource = SequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('support::filament/resources/sequence/pages/manage-sequences.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/sequence/pages/manage-sequences.header-actions.create.notification.title'))
                        ->body(__('support::filament/resources/sequence/pages/manage-sequences.header-actions.create.notification.body')),
                ),
        ];
    }
}
