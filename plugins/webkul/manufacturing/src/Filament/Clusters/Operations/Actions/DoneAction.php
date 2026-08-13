<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Component;
use Throwable;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Facades\Manufacturing as ManufacturingFacade;
use Webkul\Manufacturing\Models\Order;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;

class DoneAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'manufacturing.order.done';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('manufacturing::filament/clusters/operations/actions/done.label'))
            ->label(function (Order $record) {
                if (
                    $record->quantity_producing == 0
                    || $record->quantity_producing == $record->quantity
                ) {
                    return __('manufacturing::filament/clusters/operations/actions/done.label');
                }

                return __('manufacturing::filament/clusters/operations/actions/done.partial-label');
            })
            ->modal(fn (Order $record) => $this->hasAnyCondition($record))
            ->mountUsing(function (Order $record, Schema $form): void {
                try {
                    $record->checkSnUniqueness();

                    if (! float_is_zero($record->quantity_producing, precisionRounding: $record->uom->rounding)) {
                        $record->rawMaterialMoves
                            ->filter(fn ($move) => $move->manual_consumption && ! $move->is_picked)
                            ->each(fn ($move) => $move->update(['is_picked' => true]));
                    } else {
                        if ($record->autoProductionChecks()) {
                            $record->setQuantities();
                        } else {
                            // return $record->actionMassProduce();
                        }
                    }

                    $record->refresh();
                } catch (Throwable $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt();

                    return;
                }

                $form->fill($this->buildFormData($record));
            })
            ->form($this->buildForm())
            ->extraModalFooterActions(fn (Order $record) => $this->getExtraFooterActions($record))
            ->action(function (Order $record, Component $livewire): void {
                $this->executeDone($record, $livewire);
            })
            ->hidden(fn () => ! in_array($this->getRecord()->state, [
                ManufacturingOrderState::CONFIRMED,
                ManufacturingOrderState::PROGRESS,
                ManufacturingOrderState::TO_CLOSE,
            ]));
    }

    private function hasAnyCondition(Order $record): bool
    {
        return $this->hasConsumptionIssues($record)
            || $this->hasQuantityIssues($record);
    }

    public function getModalHeading(): string|Htmlable
    {
        $record = $this->getRecord();

        if ($record instanceof Order && $this->hasConsumptionIssues($record)) {
            return __('manufacturing::filament/clusters/operations/actions/done.modal.consumption-warning.heading');
        }

        if ($record instanceof Order && $this->hasQuantityIssues($record)) {
            return __('manufacturing::filament/clusters/operations/actions/done.modal.produced-warning.heading');
        }

        return parent::getModalHeading();
    }

    public function getModalDescription(): string|Htmlable|null
    {
        $record = $this->getRecord();

        if ($record instanceof Order && $this->hasConsumptionIssues($record)) {
            return __('manufacturing::filament/clusters/operations/actions/done.modal.consumption-warning.description');
        }

        if ($record instanceof Order && $this->hasQuantityIssues($record)) {
            return __('manufacturing::filament/clusters/operations/actions/done.modal.produced-warning.description');
        }

        return parent::getModalDescription();
    }

    public function getModalSubmitActionLabel(): string
    {
        $record = $this->getRecord();

        if ($record instanceof Order && $this->hasConsumptionIssues($record)) {
            return __('manufacturing::filament/clusters/operations/actions/done.modal.consumption-warning.actions.confirm.label');
        }

        return parent::getModalSubmitActionLabel();
    }

    /**
     * @return array<Action>
     */
    private function getExtraFooterActions(Order $record): array
    {
        if ($this->hasConsumptionIssues($record)) {
            return [
                Action::make('set-quantities')
                    ->label(__('manufacturing::filament/clusters/operations/actions/done.modal.consumption-warning.actions.set-quantities.label'))
                    ->color('gray')
                    ->cancelParentActions()
                    ->databaseTransaction()
                    ->action(function (Order $record, Component $livewire): void {
                        $this->setQuantities($record, $livewire);
                    }),
            ];
        }

        return [];
    }

    private function buildFormData(Order $record): array
    {
        $data = [];

        if ($this->hasConsumptionIssues($record)) {
            $data['consumed_moves_issues'] = $this->getConsumedMovesData($record);
        }

        return $data;
    }

    private function buildForm(): array
    {
        return [
            $this->makeConsumedIssuesRepeater(),
        ];
    }

    private function hasConsumptionIssues(Order $record): bool
    {
        return ! empty($record->getConsumptionIssues());
    }

    private function hasQuantityIssues(Order $record): bool
    {
        return ! empty($record->getQuantityProducedIssues());
    }

    private function makeConsumedIssuesRepeater(): Repeater
    {
        return Repeater::make('consumed_moves_issues')
            ->hiddenLabel()
            ->deletable(false)
            ->addable(false)
            ->reorderable(false)
            ->visible(fn (Order $record) => $this->hasConsumptionIssues($record))
            ->table([
                RepeaterTableColumn::make('product_name')
                    ->label(__('manufacturing::filament/clusters/operations/actions/done.modal.consumption-warning.form.product')),
                RepeaterTableColumn::make('uom')
                    ->label(__('manufacturing::filament/clusters/operations/actions/done.modal.consumption-warning.form.uom')),
                RepeaterTableColumn::make('to_consume')
                    ->label(__('manufacturing::filament/clusters/operations/actions/done.modal.consumption-warning.form.to-consume')),
                RepeaterTableColumn::make('consumed')
                    ->label(__('manufacturing::filament/clusters/operations/actions/done.modal.consumption-warning.form.consumed')),
            ])
            ->schema([
                TextEntry::make('product_name')->hiddenLabel(),
                TextEntry::make('uom')->hiddenLabel(),
                TextEntry::make('to_consume')->hiddenLabel(),
                TextEntry::make('consumed')->hiddenLabel(),
            ]);
    }

    private function getConsumedMovesData(Order $record): array
    {
        return collect($record->getConsumptionIssues())
            ->map(fn ($issue) => [
                'product_name' => $issue[1]->name,
                'to_consume'   => $issue[3],
                'consumed'     => $issue[2],
                'uom'          => $issue[1]->uom->name,
            ])
            ->values()
            ->all();
    }

    private function executeDone(Order $record, Component $livewire): void
    {
        try {
            ManufacturingFacade::doneManufacturingOrder($record);

            $record->refresh();

            $livewire->updateForm();

            Notification::make()
                ->success()
                ->title(__('manufacturing::filament/clusters/operations/actions/done.notification.success.title'))
                ->body(__('manufacturing::filament/clusters/operations/actions/done.notification.success.body'))
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->body($e->getMessage())
                ->send();

            $this->halt(shouldRollBackDatabaseTransaction: true);
        }
    }

    public function setQuantities(Order $record, Component $livewire)
    {
        $consumptionWarningLines = $record->getConsumptionIssues();

        foreach ($consumptionWarningLines as [, $product, $consumed, $toConsume]) {
            $rawMaterialMoves = $record->rawMaterialMoves->filter(fn ($move) => $move->product_id === $product->id);

            foreach ($rawMaterialMoves as $move) {
                $qtyExpected = $product->uom->computeQuantity($toConsume, $move->uom);

                $values = ['is_picked' => true];

                if (float_compare($qtyExpected, $move->quantity, precisionRounding: $move->uom->rounding) !== 0) {
                    $values['quantity'] = $qtyExpected;
                }

                $move->update($values);
            }
        }

        $this->executeDone($record, $livewire);
    }
}
