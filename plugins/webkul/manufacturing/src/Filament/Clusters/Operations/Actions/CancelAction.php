<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Facades\Manufacturing as ManufacturingFacade;
use Webkul\Manufacturing\Models\Order;

class CancelAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'manufacturing.order.cancel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('manufacturing::filament/clusters/operations/actions/cancel.label'))
            ->color('gray')
            ->requiresConfirmation()
            ->action(function (Order $record, Component $livewire): void {
                try {
                    $record = ManufacturingFacade::cancelManufacturingOrder($record);

                    $livewire->updateForm();

                    Notification::make()
                        ->success()
                        ->title(__('manufacturing::filament/clusters/operations/actions/cancel.notification.success.title'))
                        ->body(__('manufacturing::filament/clusters/operations/actions/cancel.notification.success.body'))
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->hidden(fn () => in_array($this->getRecord()->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL]));
    }
}
