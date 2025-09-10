<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions\Print;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Webkul\Inventory\Settings\TraceabilitySettings;

class LabelsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.print.labels';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/print/labels.label'))
            ->schema([
                Wizard::make([
                    Step::make(__('inventories::filament/clusters/operations/actions/print/labels.form.fields.type'))
                        ->schema([
                            Radio::make('type')
                                ->label('Type')
                                ->options([
                                    'product' => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.type-options.product'),
                                    'lot'     => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.type-options.lot'),
                                ])
                                ->default('product')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    if ($get('type') === 'product') {
                                        $set('quantity_type', 'operation');
                                        $set('quantity', null);
                                        $set('format', '2x7_price');
                                    } else {
                                        $set('quantity_type', 'per_lot');
                                        $set('quantity', null);
                                        $set('format', '4x12');
                                    }
                                }),
                        ])
                        ->visible(function (TraceabilitySettings $settings, $record) {
                            if (! $settings->enable_lots_serial_numbers) {
                                return false;
                            }

                            return $record->moveLines->contains(fn ($moveLine): bool => (bool) $moveLine->lot_id);
                        }),
                    Step::make(__('inventories::filament/clusters/operations/actions/print/labels.form.fields.layout'))
                        ->schema([
                            Group::make()
                                ->schema([
                                    Select::make('quantity_type')
                                        ->label(__('inventories::filament/clusters/operations/actions/print/labels.form.fields.quantity-type'))
                                        ->options([
                                            'operation' => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.quantity-type-options.operation'),
                                            'custom'    => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.quantity-type-options.custom'),
                                        ])
                                        ->default('operation')
                                        ->live(),
                                    TextInput::make('quantity')
                                        ->label(__('inventories::filament/clusters/operations/actions/print/labels.form.fields.quantity'))
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(100)
                                        ->visible(fn (Get $get): bool => $get('quantity_type') === 'custom'),
                                    Radio::make('format')
                                        ->label(__('inventories::filament/clusters/operations/actions/print/labels.form.fields.format'))
                                        ->options([
                                            'dymo'       => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.format-options.dymo'),
                                            '2x7_price'  => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.format-options.2x7_price'),
                                            '4x7_price'  => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.format-options.4x7_price'),
                                            '4x12'       => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.format-options.4x12'),
                                            '4x12_price' => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.format-options.4x12_price'),
                                        ])
                                        ->default('2x7_price')
                                        ->required(),
                                ])
                                ->visible(fn (Get $get): bool => $get('type') === 'product'),
                            Group::make()
                                ->schema([
                                    Radio::make('quantity_type')
                                        ->label(__('inventories::filament/clusters/operations/actions/print/labels.form.fields.quantity-type'))
                                        ->options([
                                            'per_lot'  => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.quantity-type-options.per-slot'),
                                            'per_unit' => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.quantity-type-options.per-unit'),
                                        ])
                                        ->default('per_lot')
                                        ->required()
                                        ->live(),
                                    Radio::make('format')
                                        ->label(__('inventories::filament/clusters/operations/actions/print/labels.form.fields.format'))
                                        ->options([
                                            '4x12' => __('inventories::filament/clusters/operations/actions/print/labels.form.fields.format-options.4x12'),
                                        ])
                                        ->required()
                                        ->default('4x12'),
                                ])
                                ->visible(fn (Get $get): bool => $get('type') === 'lot'),
                        ]),
                ]),
            ])
            ->action(function (array $data, $record) {
                $pdf = PDF::loadView('inventories::filament.clusters.operations.actions.labels', [
                    'type'         => $data['type'] ?? 'product',
                    'quantityType' => $data['quantity_type'] ?? 1,
                    'quantity'     => $data['quantity'] ?? 1,
                    'format'       => $data['format'],
                    'records'      => $record->moveLines,
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
