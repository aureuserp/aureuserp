<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditWarehouse extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = WarehouseResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return DB::transaction(fn () => parent::handleRecordUpdate($record, $data));
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.notification.error.title'))
                ->body($e->getMessage())
                ->send();

            throw new Halt;
        }
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.header-actions.delete.notification.body')),
                ),
        ];
    }
}
