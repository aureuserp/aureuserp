<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\NoteResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\NoteResource;

class ManageNotes extends ManageRecords
{
    protected static string $resource = NoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/note/pages/manage-notes.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/note/pages/manage-notes.header-actions.create.notification.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/note/pages/manage-notes.header-actions.create.notification.body')),
                ),
        ];
    }
}
