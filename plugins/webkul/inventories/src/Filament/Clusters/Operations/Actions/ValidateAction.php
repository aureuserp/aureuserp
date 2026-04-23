<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\ProductQuantity;

class ValidateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.validate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/validate.label'))
            ->color(fn (Operation $record) => $this->resolveColor($record))
            ->hidden(fn (Operation $record) => $this->isFinalized($record))
            ->requiresConfirmation(fn (Operation $record) => $this->shouldAskForBackorder($record))
            ->modalHeading(fn (Operation $record) => $this->shouldAskForBackorder($record)
                ? __('inventories::filament/clusters/operations/actions/validate.modal-heading')
                : null
            )
            ->modalDescription(fn (Operation $record) => $this->shouldAskForBackorder($record)
                ? __('inventories::filament/clusters/operations/actions/validate.modal-description')
                : null
            )
            ->extraModalFooterActions(fn (Operation $record) => $this->shouldAskForBackorder($record)
                ? [$this->makeNoBackorderAction()]
                : []
            )
            ->action(fn (Operation $record, Component $livewire) => $this->validateWithBackorder($record, $livewire));
    }

    protected function isFinalized(Operation $record): bool
    {
        return in_array($record->state, [
            OperationState::DONE,
            OperationState::CANCELED,
        ]);
    }

    protected function resolveColor(Operation $record): string
    {
        $isEarlyStage = in_array($record->state, [
            OperationState::DRAFT,
            OperationState::CONFIRMED,
        ]);

        return $isEarlyStage ? 'gray' : 'primary';
    }

    protected function shouldAskForBackorder(Operation $record): bool
    {
        return $record->operationType->create_backorder === CreateBackorder::ASK
            && Inventory::canCreateBackOrder($record);
    }

    protected function validateWithBackorder(Operation $record, Component $livewire): void
    {
        if ($this->hasMoveErrors($record)) {
            return;
        }

        Inventory::createBackOrder($record);
        Inventory::validateTransfer($record);

        $livewire->updateForm();
    }

    protected function makeNoBackorderAction(): Action
    {
        return Action::make('no-backorder')
            ->label(__('inventories::filament/clusters/operations/actions/validate.extra-modal-footer-actions.no-backorder.label'))
            ->color('danger')
            ->extraAttributes(['class' => 'whitespace-nowrap'])
            ->cancelParentActions()
            ->action(function (Operation $record, Component $livewire): void {
                if ($this->hasMoveErrors($record)) {
                    return;
                }

                Inventory::validateTransfer($record, skipReceiptCreation: true);

                $livewire->updateForm();
            });
    }

    protected function hasMoveErrors(Operation $record): bool
    {
        $record = Inventory::computeTransfer($record);

        foreach ($record->moves as $move) {
            if ($this->hasMoveLineErrors($move)) {
                return true;
            }
        }

        return false;
    }

    private function hasMoveLineErrors($move): bool
    {
        if ($this->hasNoLines($move)) {
            return true;
        }

        if ($this->hasInvalidPartialPackage($move)) {
            return true;
        }

        if ($this->hasMissingLot($move)) {
            return true;
        }

        if ($this->hasInvalidSerialTracking($move)) {
            return true;
        }

        return false;
    }

    private function hasNoLines($move): bool
    {
        if ($move->lines->isNotEmpty()) {
            return false;
        }

        $this->warn('lines-missing');

        return true;
    }

    private function hasInvalidPartialPackage($move): bool
    {
        foreach ($move->lines as $line) {
            $sameSourceAndResultPackage = $line->package_id
                && $line->result_package_id
                && $line->package_id == $line->result_package_id;

            if (! $sameSourceAndResultPackage) {
                continue;
            }

            $sourceQuantity = ProductQuantity::query()
                ->where('product_id', $line->product_id)
                ->where('location_id', $line->source_location_id)
                ->where('lot_id', $line->lot_id)
                ->where('package_id', $line->package_id)
                ->first();

            if ($sourceQuantity && $sourceQuantity->quantity != $line->qty) {
                $this->warn('partial-package');

                return true;
            }
        }

        return false;
    }

    private function hasMissingLot($move): bool
    {
        $requiresLot = in_array($move->product->tracking, [
            ProductTracking::LOT,
            ProductTracking::SERIAL,
        ]);

        if (! $requiresLot) {
            return false;
        }

        $missingLot = $move->lines->contains(fn ($line) => ! $line->lot_id);

        if (! $missingLot) {
            return false;
        }

        $this->warn('lot-missing');

        return true;
    }

    private function hasInvalidSerialTracking($move): bool
    {
        if ($move->product->tracking !== ProductTracking::SERIAL) {
            return false;
        }

        $hasNonUnitQty = $move->lines->contains(fn ($line) => $line->qty != 1);

        if ($hasNonUnitQty) {
            $this->warn('serial-qty');

            return true;
        }

        $lots = $move->lines->pluck('lot_id');

        if ($lots->count() !== $lots->unique()->count()) {
            $this->warn('serial-qty');

            return true;
        }

        return false;
    }

    private function warn(string $key): void
    {
        $this->sendNotification(
            "inventories::filament/clusters/operations/actions/validate.notification.warning.{$key}.title",
            "inventories::filament/clusters/operations/actions/validate.notification.warning.{$key}.body",
            'warning'
        );
    }

    private function sendNotification(string $titleKey, string $bodyKey, string $type = 'info'): void
    {
        Notification::make()
            ->title(__($titleKey))
            ->body(__($bodyKey))
            ->{$type}()
            ->send();
    }
}
