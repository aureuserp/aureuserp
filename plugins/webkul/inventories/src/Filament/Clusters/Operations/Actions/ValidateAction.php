<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Models\Operation;

class ValidateAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.validate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('inventories::filament/clusters/operations/actions/validate.label'))
            ->color(function (Operation $record) {
                if (in_array($record->state, [OperationState::DRAFT, OperationState::CONFIRMED])) {
                    return 'gray';
                }

                return 'primary';
            })
            ->requiresConfirmation(fn (Operation $record) => $this->shouldAskBackOrder($record))
            ->modalHeading(fn (Operation $record) => $this->shouldAskBackOrder($record)
                ? __('inventories::filament/clusters/operations/actions/validate.modal-heading')
                : null)
            ->modalDescription(fn (Operation $record) => $this->shouldAskBackOrder($record)
                ? __('inventories::filament/clusters/operations/actions/validate.modal-description')
                : null)
            ->extraModalFooterActions(fn (Operation $record) => $this->shouldAskBackOrder($record) ? [
                Action::make('no-backorder')
                    ->label(__('inventories::filament/clusters/operations/actions/validate.extra-modal-footer-actions.no-backorder.label'))
                    ->color('danger')
                    ->cancelParentActions()
                    ->databaseTransaction()
                    ->action(function (Operation $record, Component $livewire): void {
                        $this->executeDoneTransfer($record, $livewire, cancelBackOrder: true);
                    }),
            ] : [])
            ->action(function (Operation $record, Component $livewire): void {
                $this->executeDoneTransfer(
                    $record,
                    $livewire,
                    cancelBackOrder: $record->operationType->create_backorder === CreateBackorder::NEVER,
                );
            })
            ->hidden(function (Operation $record) {
                return in_array($record->state, [
                    OperationState::DONE,
                    OperationState::CANCELED,
                ]);
            });
    }

    public function canCreateBackOrder(Operation $record): bool
    {
        if ($record->operationType->create_backorder === CreateBackorder::NEVER) {
            return false;
        }

        return $record->moves->sum('product_uom_qty') > $record->moves->sum('quantity');
    }

    private function shouldAskBackOrder(Operation $record): bool
    {
        return $record->operationType->create_backorder === CreateBackorder::ASK
            && $this->canCreateBackOrder($record);
    }

    private function executeDoneTransfer(Operation $record, Component $livewire, bool $cancelBackOrder = false): void
    {
        try {
            InventoryFacade::doneTransfer($record, $cancelBackOrder);

            $livewire->updateForm();
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->body($e->getMessage())
                ->send();

            $livewire->unmountAction();

            $this->halt(shouldRollBackDatabaseTransaction: true);
        }
    }
}
