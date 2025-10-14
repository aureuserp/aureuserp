<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Scrap;

class EditScrap extends EditRecord
{
    protected static string $resource = ScrapResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.notification.title'))
            ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            Action::make('validate')
                ->label(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.validate.label'))
                ->color('gray')
                ->action(function (Scrap $record) {
                    $locationQuantity = ProductQuantity::where('location_id', $record->source_location_id)
                        ->where('product_id', $record->product_id)
                        ->where('package_id', $record->package_id ?? null)
                        ->where('lot_id', $record->lot_id ?? null)
                        ->first();

                    if (! $locationQuantity || $locationQuantity->quantity < $record->qty) {
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.validate.notification.warning.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.validate.notification.warning.body'))
                            ->warning()
                            ->send();

                        return;
                    }

                    $locationQuantity->update([
                        'quantity' => $locationQuantity->quantity - $record->qty,
                    ]);

                    $destinationQuantity = ProductQuantity::where('product_id', $record->product_id)
                        ->where('location_id', $record->destination_location_id)
                        ->first();

                    if ($destinationQuantity) {
                        $destinationQuantity->update([
                            'quantity'                => $destinationQuantity->quantity + $record->qty,
                            'reserved_quantity'       => $destinationQuantity->reserved_quantity + $record->qty,
                            'inventory_diff_quantity' => $destinationQuantity->inventory_diff_quantity - $record->qty,
                        ]);
                    } else {
                        ProductQuantity::create([
                            'product_id'              => $record->product_id,
                            'location_id'             => $record->destination_location_id,
                            'quantity'                => $record->qty,
                            'reserved_quantity'       => $record->qty,
                            'inventory_diff_quantity' => -$record->qty,
                            'incoming_at'             => now(),
                            'creator_id'              => Auth::id(),
                            'company_id'              => $record->destinationLocation->company_id,
                        ]);
                    }

                    $record->update([
                        'state'     => ScrapState::DONE,
                        'closed_at' => now(),
                    ]);

                    $move = ProductResource::createMove($record, $record->qty, $record->source_location_id, $record->destination_location_id);

                    $move->update([
                        'scrap_id' => $record->id,
                    ]);
                })
                ->hidden(fn () => $this->getRecord()->state == ScrapState::DONE),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == ScrapState::DONE)
                ->action(function (DeleteAction $action, Scrap $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/operations/resources/scrap/pages/edit-scrap.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
