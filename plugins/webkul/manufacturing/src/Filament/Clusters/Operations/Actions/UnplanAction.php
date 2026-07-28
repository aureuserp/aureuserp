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

class UnplanAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'manufacturing.order.unplan';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('manufacturing::filament/clusters/operations/actions/unplan.label'))
            ->requiresConfirmation()
            ->color('gray')
            ->action(function (Order $record, Component $livewire): void {
                try {
                    $record = ManufacturingFacade::unplanManufacturingOrder($record);

                    $livewire->updateForm();

                    Notification::make()
                        ->success()
                        ->title(__('manufacturing::filament/clusters/operations/actions/unplan.notification.success.title'))
                        ->body(__('manufacturing::filament/clusters/operations/actions/unplan.notification.success.body'))
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn (Order $record) => ! in_array($record->state, [ManufacturingOrderState::DRAFT, ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL]) && $record->is_planned);
    }
}
