<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewOperation extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = OperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('manufacturing::filament/clusters/configurations/resources/operation/pages/view-operation.header-actions.delete.notification.title'))
                        ->body(__('manufacturing::filament/clusters/configurations/resources/operation/pages/view-operation.header-actions.delete.notification.body')),
                ),
        ];
    }
}
