<?php

namespace Webkul\Product\Filament\Resources\PriceListResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Product\Filament\Resources\PriceListResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPriceList extends EditRecord
{
    use HasRecordNavigationTabs;
    use HasRepeaterColumnManager;

    protected static string $resource = PriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/price-list/pages/edit-price-list.header-actions.delete.notification.title'))
                        ->body(__('products::filament/resources/price-list/pages/edit-price-list.header-actions.delete.notification.body')),
                ),
        ];
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('products::filament/resources/price-list/pages/edit-price-list.notification.title'))
            ->body(__('products::filament/resources/price-list/pages/edit-price-list.notification.body'));
    }
}
