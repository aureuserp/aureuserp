<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Actions\Print;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Models\Order;

class PrintLabelsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'manufacturing.operations.print.labels';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('manufacturing::filament/clusters/operations/actions/print/print-labels.label'))
            ->schema([
                Group::make()
                    ->schema([
                        Select::make('quantity_type')
                            ->label(__('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.quantity-type'))
                            ->options([
                                'operation' => __('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.quantity-type-options.operation'),
                                'custom'    => __('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.quantity-type-options.custom'),
                            ])
                            ->default('operation')
                            ->live(),
                        TextInput::make('quantity')
                            ->label(__('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.quantity'))
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(100)
                            ->visible(fn (Get $get): bool => $get('quantity_type') === 'custom'),
                        Radio::make('format')
                            ->label(__('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.format'))
                            ->options([
                                'dymo'       => __('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.format-options.dymo'),
                                '2x7_price'  => __('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.format-options.2x7_price'),
                                '4x7_price'  => __('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.format-options.4x7_price'),
                                '4x12'       => __('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.format-options.4x12'),
                                '4x12_price' => __('manufacturing::filament/clusters/operations/actions/print/print-labels.form.fields.format-options.4x12_price'),
                            ])
                            ->default('2x7_price')
                            ->required(),
                    ]),
            ])
            ->action(function (array $data, Order $record) {
                $record->load([
                    'product',
                    'rawMaterialMoves.product',
                    'rawMaterialMoves.uom',
                    'finishedMoves.product',
                    'finishedMoves.uom',
                ]);

                $isDone = $record->state === ManufacturingOrderState::DONE;

                $pdf = Pdf::loadView('manufacturing::filament.clusters.operations.actions.print.labels', [
                    'record'       => $record,
                    'isDone'       => $isDone,
                    'quantityType' => $data['quantity_type'] ?? 'operation',
                    'quantity'     => (int) ($data['quantity'] ?? 1),
                    'format'       => $data['format'],
                ]);

                $paperSize = match ($data['format']) {
                    'dymo'  => [0, 0, 252.2, 144],
                    default => 'a4',
                };

                $pdf->setPaper($paperSize, 'portrait');

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, 'Labels-'.str_replace('/', '_', $record->name).'.pdf');
            });
    }
}
