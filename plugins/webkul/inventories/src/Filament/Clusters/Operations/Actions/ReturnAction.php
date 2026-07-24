<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Component;
use Throwable;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Operation;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;

class ReturnAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.return';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/return.label'))
            ->color('gray')
            ->mountUsing(function (Operation $record, Schema $form): void {
                $returnableMoves = $this->getReturnableMoves($record);

                if ($returnableMoves->isEmpty()) {
                    Notification::make()
                        ->warning()
                        ->body(__('inventories::filament/clusters/operations/actions/return.notification.no-products.body'))
                        ->send();

                    $this->halt();

                    return;
                }

                $form->fill([
                    'return_moves' => $returnableMoves->map(fn ($move) => [
                        'move_id'       => $move->id,
                        'move_quantity' => (float) $move->quantity,
                        'product_name'  => $move->product?->name ?? '—',
                        'qty'           => $move->product_uom_qty,
                        'uom_name'      => $move->uom?->name ?? '—',
                    ])->values()->all(),
                ]);
            })
            ->form([
                Repeater::make('return_moves')
                    ->hiddenLabel()
                    ->deletable(true)
                    ->addable(false)
                    ->reorderable(false)
                    ->compact()
                    ->table([
                        RepeaterTableColumn::make('product_name')
                            ->label(__('inventories::filament/clusters/operations/actions/return.modal.form.columns.product')),
                        RepeaterTableColumn::make('qty')
                            ->label(__('inventories::filament/clusters/operations/actions/return.modal.form.columns.quantity')),
                        RepeaterTableColumn::make('uom_name')
                            ->label(__('inventories::filament/clusters/operations/actions/return.modal.form.columns.uom')),
                    ])
                    ->schema([
                        Hidden::make('move_id'),
                        Hidden::make('move_quantity'),
                        TextEntry::make('product_name')->hiddenLabel(),
                        TextInput::make('qty')
                            ->hiddenLabel()
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->live(onBlur: true)
                            ->suffix(function (Get $get): mixed {
                                if ((float) ($get('qty') ?? 0) <= (float) ($get('move_quantity') ?? 0)) {
                                    return null;
                                }

                                return \Filament\Support\generate_icon_html(
                                    'heroicon-o-exclamation-triangle',
                                    null,
                                    (new ComponentAttributeBag)
                                        ->color(IconComponent::class, 'warning')
                                        ->class(['fi-text-color-600'])
                                        ->merge([
                                            'style'         => 'color: var(--text)',
                                            'x-tooltip.raw' => __('inventories::filament/clusters/operations/actions/return.modal.form.columns.excess-quantity-tooltip'),
                                        ], escape: false),
                                );
                            }),
                        TextEntry::make('uom_name')->hiddenLabel(),
                    ]),
            ])
            ->action(function (Operation $record, array $data, Component $livewire): void {
                $moveQuantities = collect($data['return_moves'] ?? [])
                    ->filter(fn ($row) => (float) $row['qty'] > 0)
                    ->mapWithKeys(fn ($row) => [(int) $row['move_id'] => (float) $row['qty']])
                    ->all();

                if (empty($moveQuantities)) {
                    Notification::make()
                        ->warning()
                        ->body(__('inventories::filament/clusters/operations/actions/return.notification.no-quantities.body'))
                        ->send();

                    $this->halt();

                    return;
                }

                try {
                    $newRecord = Inventory::returnTransfer($record, $moveQuantities);

                    $livewire->updateForm();

                    redirect()->to(OperationResource::getUrl('edit', ['record' => $newRecord]));
                } catch (Throwable $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn () => $this->getRecord()->state == OperationState::DONE);
    }

    private function getReturnableMoves(Operation $record): Collection
    {
        return $record->moves->filter(
            fn ($move) => $move->state === MoveState::DONE && ! $move->is_scraped
        );
    }
}
