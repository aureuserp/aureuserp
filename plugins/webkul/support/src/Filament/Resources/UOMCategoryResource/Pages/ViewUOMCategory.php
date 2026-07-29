<?php

namespace Webkul\Support\Filament\Resources\UOMCategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Support\Filament\Resources\UOMCategoryResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewUOMCategory extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = UOMCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/uom-category/pages/view-uom-category.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/uom-category/pages/view-uom-category.header-actions.delete.notification.body')),
                ),
        ];
    }
}
