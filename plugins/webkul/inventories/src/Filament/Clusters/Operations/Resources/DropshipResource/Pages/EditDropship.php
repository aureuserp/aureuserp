<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Actions as OperationActions;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource;
use Webkul\Inventory\Models\Dropship;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditDropship extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = DropshipResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/operations/resources/dropship/pages/edit-dropship.notification.title'))
            ->body(__('inventories::filament/clusters/operations/resources/dropship/pages/edit-dropship.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            OperationActions\TodoAction::make(),
            OperationActions\ValidateAction::make(),
            OperationActions\CancelAction::make(),
            OperationActions\ReturnAction::make(),
            ActionGroup::make([
                OperationActions\Print\PickingOperationAction::make(),
                OperationActions\Print\DeliverySlipAction::make(),
                OperationActions\Print\PackageAction::make(),
                OperationActions\Print\LabelsAction::make(),
            ])
                ->label(__('inventories::filament/clusters/operations/resources/dropship/pages/edit-dropship.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->button(),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == OperationState::DONE)
                ->action(function (DeleteAction $action, Dropship $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/operations/resources/dropship/pages/edit-dropship.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/dropship/pages/edit-dropship.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/operations/resources/dropship/pages/edit-dropship.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/operations/resources/dropship/pages/edit-dropship.header-actions.delete.notification.success.body')),
                ),
        ];
    }

    public function updateForm(): void
    {
        $this->fillForm();
    }
}
