<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditWarehouse extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = WarehouseResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/edit-warehouse.header-actions.delete.notification.body')),
                ),
        ];
    }

    protected function afterSave(): void
    {
        $warehouse = $this->getRecord();

        $supplierLocation = Location::where('type', LocationType::SUPPLIER)->first();

        $customerLocation = Location::where('type', LocationType::CUSTOMER)->first();

        $this->updateLocations(
            'reception_steps',
            [
                ReceptionStep::ONE_STEP->value => [
                    'archive' => [$warehouse->input_stock_location_id, $warehouse->qc_stock_location_id],
                ],
                ReceptionStep::TWO_STEPS->value => [
                    'restore' => [$warehouse->input_stock_location_id],
                    'archive' => [$warehouse->qc_stock_location_id],
                ],
                ReceptionStep::THREE_STEPS->value => [
                    'restore' => [$warehouse->input_stock_location_id, $warehouse->qc_stock_location_id],
                ],
            ]
        );

        $this->updateLocations(
            'delivery_steps',
            [
                DeliveryStep::ONE_STEP->value => [
                    'archive' => [$warehouse->output_stock_location_id, $warehouse->pack_stock_location_id],
                ],
                DeliveryStep::TWO_STEPS->value => [
                    'restore' => [$warehouse->output_stock_location_id],
                    'archive' => [$warehouse->pack_stock_location_id],
                ],
                DeliveryStep::THREE_STEPS->value => [
                    'restore' => [$warehouse->output_stock_location_id, $warehouse->pack_stock_location_id],
                ],
            ]
        );

        $this->updateOperationTypes(
            'reception_steps',
            [
                ReceptionStep::ONE_STEP->value => [
                    'update' => [
                        $warehouse->in_type_id => [
                            'source_location_id'      => $supplierLocation->id,
                            'destination_location_id' => $warehouse->lot_stock_location_id,
                            'deleted_at'              => null,
                        ],
                    ],
                    'archive' => [$warehouse->store_type_id, $warehouse->qc_type_id],
                ],
                ReceptionStep::TWO_STEPS->value => [
                    'update' => [
                        $warehouse->in_type_id => [
                            'source_location_id'      => $supplierLocation->id,
                            'destination_location_id' => $warehouse->input_stock_location_id,
                            'deleted_at'              => null,
                        ],
                        $warehouse->store_type_id => [
                            'source_location_id'      => $warehouse->input_stock_location_id,
                            'destination_location_id' => $warehouse->lot_stock_location_id,
                            'deleted_at'              => null,
                        ],
                    ],
                    'archive' => [$warehouse->qc_type_id],
                ],
                ReceptionStep::THREE_STEPS->value => [
                    'update' => [
                        $warehouse->in_type_id => [
                            'source_location_id'      => $supplierLocation->id,
                            'destination_location_id' => $warehouse->input_stock_location_id,
                            'deleted_at'              => null,
                        ],
                        $warehouse->qc_type_id => [
                            'source_location_id'      => $warehouse->input_stock_location_id,
                            'destination_location_id' => $warehouse->qc_stock_location_id,
                            'deleted_at'              => null,
                        ],
                        $warehouse->store_type_id => [
                            'source_location_id'      => $warehouse->qc_stock_location_id,
                            'destination_location_id' => $warehouse->lot_stock_location_id,
                            'deleted_at'              => null,
                        ],
                    ],
                ],
            ]
        );

        $this->updateOperationTypes(
            'delivery_steps',
            [
                DeliveryStep::ONE_STEP->value => [
                    'update' => [
                        $warehouse->out_type_id => [
                            'source_location_id'      => $warehouse->lot_stock_location_id,
                            'destination_location_id' => $customerLocation->id,
                            'deleted_at'              => null,
                        ],
                    ],
                    'archive' => [$warehouse->pick_type_id, $warehouse->pack_type_id],
                ],
                DeliveryStep::TWO_STEPS->value => [
                    'update' => [
                        $warehouse->pick_type_id => [
                            'source_location_id'      => $warehouse->lot_stock_location_id,
                            'destination_location_id' => $warehouse->output_stock_location_id,
                            'deleted_at'              => null,
                        ],
                        $warehouse->out_type_id => [
                            'source_location_id'      => $warehouse->output_stock_location_id,
                            'destination_location_id' => $customerLocation->id,
                            'deleted_at'              => null,
                        ],
                    ],
                    'archive' => [$warehouse->pack_type_id],
                ],
                DeliveryStep::THREE_STEPS->value => [
                    'update' => [
                        $warehouse->pick_type_id => [
                            'source_location_id'      => $warehouse->lot_stock_location_id,
                            'destination_location_id' => $warehouse->pack_stock_location_id,
                            'deleted_at'              => null,
                        ],
                        $warehouse->pack_type_id => [
                            'source_location_id'      => $warehouse->pack_stock_location_id,
                            'destination_location_id' => $warehouse->output_stock_location_id,
                            'deleted_at'              => null,
                        ],
                        $warehouse->out_type_id => [
                            'source_location_id'      => $warehouse->output_stock_location_id,
                            'destination_location_id' => $customerLocation->id,
                            'deleted_at'              => null,
                        ],
                    ],
                ],
            ]
        );

        if (
            in_array($this->data['reception_steps'], [ReceptionStep::TWO_STEPS->value, ReceptionStep::THREE_STEPS->value])
            && in_array($this->data['delivery_steps'], [DeliveryStep::TWO_STEPS->value, DeliveryStep::THREE_STEPS->value])
        ) {
            OperationType::withTrashed()->whereIn('id', [$warehouse->xdock_type_id])->update(['deleted_at' => null]);

            Route::withTrashed()->whereIn('id', [$warehouse->crossdock_route_id])->update(['deleted_at' => null]);

            Rule::withTrashed()->where('route_id', $warehouse->crossdock_route_id)->update(['deleted_at' => null]);
        } else {
            OperationType::withTrashed()->whereIn('id', [$warehouse->xdock_type_id])->update(['deleted_at' => now()]);

            Route::withTrashed()->whereIn('id', [$warehouse->crossdock_route_id])->update(['deleted_at' => now()]);

            Rule::withTrashed()->where('route_id', $warehouse->crossdock_route_id)->update(['deleted_at' => now()]);
        }

        $warehouse->receptionRoute->update([
            'name' => match ($this->data['reception_steps']) {
                ReceptionStep::ONE_STEP->value    => $warehouse->name.': Receive in 1 step (Stock)',
                ReceptionStep::TWO_STEPS->value   => $warehouse->name.': Receive in 2 steps (Input + Stock)',
                ReceptionStep::THREE_STEPS->value => $warehouse->name.': Receive in 3 steps (Input + Quality + Stock)',
            },
        ]);

        $warehouse->deliveryRoute->update([
            'name' => match ($this->data['delivery_steps']) {
                DeliveryStep::ONE_STEP->value    => $warehouse->name.': Deliver in 1 step (Ship)',
                DeliveryStep::TWO_STEPS->value   => $warehouse->name.': Deliver in 2 steps (Pick + Ship)',
                DeliveryStep::THREE_STEPS->value => $warehouse->name.': Deliver in 3 steps (Pick + Pack + Ship)',
            },
        ]);

        $this->updateRules(
            'reception_steps',
            [
                ReceptionStep::ONE_STEP->value => [
                    'restore' => [
                        // WH: Vendors → Stock => Partners/Vendors → WH/Stock
                        ['source_location_id' => $supplierLocation->id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->in_type_id],
                    ],
                    'archive' => [
                        // WH: Input → Quality Control => WH/Input → WH/Quality Control
                        ['source_location_id' => $warehouse->input_stock_location_id, 'destination_location_id' => $warehouse->qc_stock_location_id, 'operation_type_id' => $warehouse->qc_type_id],
                        // WH: Quality Control → Stock => WH/Quality Control → WH/Stock
                        ['source_location_id' => $warehouse->qc_stock_location_id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->store_type_id],
                        // WH: Input → Stock => WH/Input → WH/Stock
                        ['source_location_id' => $warehouse->input_stock_location_id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->store_type_id],
                    ],
                ],
                ReceptionStep::TWO_STEPS->value => [
                    'restore' => [
                        // WH: Vendors → Stock => Partners/Vendors → WH/Stock
                        ['source_location_id' => $supplierLocation->id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->in_type_id],
                        // WH: Input → Stock => WH/Input → WH/Stock
                        ['source_location_id' => $warehouse->input_stock_location_id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->store_type_id],
                    ],
                    'archive' => [
                        // WH: Input → Quality Control => WH/Input → WH/Quality Control
                        ['source_location_id' => $warehouse->input_stock_location_id, 'destination_location_id' => $warehouse->qc_stock_location_id, 'operation_type_id' => $warehouse->qc_type_id],
                        // WH: Quality Control → Stock => WH/Quality Control → WH/Stock
                        ['source_location_id' => $warehouse->qc_stock_location_id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->store_type_id],
                    ],
                ],
                ReceptionStep::THREE_STEPS->value => [
                    'restore' => [
                        // WH: Vendors → Stock => Partners/Vendors → WH/Stock
                        ['source_location_id' => $supplierLocation->id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->in_type_id],
                        // WH: Input → Quality Control => WH/Input → WH/Quality Control
                        ['source_location_id' => $warehouse->input_stock_location_id, 'destination_location_id' => $warehouse->qc_stock_location_id, 'operation_type_id' => $warehouse->qc_type_id],
                        // WH: Quality Control → Stock => WH/Quality Control → WH/Stock
                        ['source_location_id' => $warehouse->qc_stock_location_id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->store_type_id],
                    ],
                    'archive' => [
                        // WH: Input → Stock => WH/Input → WH/Stock
                        ['source_location_id' => $warehouse->input_stock_location_id, 'destination_location_id' => $warehouse->lot_stock_location_id, 'operation_type_id' => $warehouse->store_type_id],
                    ],
                ],
            ]
        );

        $this->updateRules(
            'delivery_steps',
            [
                DeliveryStep::ONE_STEP->value => [
                    'restore' => [
                        // WH: Stock → Customers => WH/Stock → Partners/Customers
                        ['source_location_id' => $warehouse->lot_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->out_type_id],
                    ],
                    'archive' => [
                        // WH: Stock → Customers => WH/Stock → Partners/Customers
                        ['source_location_id' => $warehouse->lot_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->pick_type_id],
                        // WH: Packing Zone → Output => WH/Packing Zone → WH/Output
                        ['source_location_id' => $warehouse->pack_stock_location_id, 'destination_location_id' => $warehouse->output_stock_location_id, 'operation_type_id' => $warehouse->pack_type_id],
                        // WH: Output → Customers => WH/Output → Partners/Customers
                        ['source_location_id' => $warehouse->output_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->out_type_id],
                    ],
                ],
                DeliveryStep::TWO_STEPS->value => [
                    'restore' => [
                        // WH: Stock → Customers => WH/Stock → Partners/Customers
                        ['source_location_id' => $warehouse->lot_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->pick_type_id],
                        // WH: Output → Customers => WH/Output → Partners/Customers
                        ['source_location_id' => $warehouse->output_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->out_type_id],
                    ],
                    'archive' => [
                        // WH: Stock → Customers => WH/Stock → Partners/Customers
                        ['source_location_id' => $warehouse->lot_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->out_type_id],
                        // WH: Packing Zone → Output => WH/Packing Zone → WH/Output
                        ['source_location_id' => $warehouse->pack_stock_location_id, 'destination_location_id' => $warehouse->output_stock_location_id, 'operation_type_id' => $warehouse->pack_type_id],
                    ],
                ],
                DeliveryStep::THREE_STEPS->value => [
                    'restore' => [
                        // WH: Stock → Customers => WH/Stock → Partners/Customers
                        ['source_location_id' => $warehouse->lot_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->pick_type_id],
                        // WH: Packing Zone → Output => WH/Packing Zone → WH/Output
                        ['source_location_id' => $warehouse->pack_stock_location_id, 'destination_location_id' => $warehouse->output_stock_location_id, 'operation_type_id' => $warehouse->pack_type_id],
                        // WH: Output → Customers => WH/Output → Partners/Customers
                        ['source_location_id' => $warehouse->output_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->out_type_id],
                    ],
                    'archive' => [
                        // WH: Stock → Customers => WH/Stock → Partners/Customers
                        ['source_location_id' => $warehouse->lot_stock_location_id, 'destination_location_id' => $customerLocation->id, 'operation_type_id' => $warehouse->out_type_id],
                    ],
                ],
            ]
        );
    }

    private function updateLocations(string $stepType, array $steps): void
    {
        $currentStep = $this->data[$stepType] ?? null;

        if (! $currentStep || ! isset($steps[$currentStep])) {
            return;
        }

        $actions = $steps[$currentStep];

        if (isset($actions['archive'])) {
            Location::withTrashed()->whereIn('id', $actions['archive'])->update(['deleted_at' => now()]);
        }

        if (isset($actions['restore'])) {
            Location::withTrashed()->whereIn('id', $actions['restore'])->update(['deleted_at' => null]);
        }
    }

    private function updateOperationTypes(string $stepType, array $steps): void
    {
        $currentStep = $this->data[$stepType] ?? null;

        if (! $currentStep || ! isset($steps[$currentStep])) {
            return;
        }

        $actions = $steps[$currentStep];

        if (isset($actions['archive'])) {
            OperationType::withTrashed()->whereIn('id', $actions['archive'])->update(['deleted_at' => now()]);
        }

        if (isset($actions['update'])) {
            foreach ($actions['update'] as $id => $update) {
                OperationType::withTrashed()
                    ->where('id', $id)
                    ->update($update);
            }
        }
    }

    private function updateRules(string $stepType, array $steps): void
    {
        $currentStep = $this->data[$stepType] ?? null;

        if (! $currentStep || ! isset($steps[$currentStep])) {
            return;
        }

        $actions = $steps[$currentStep];

        if (isset($actions['archive'])) {
            foreach ($actions['archive'] as $conditions) {
                Rule::withTrashed()
                    ->where($conditions)
                    ->update(['deleted_at' => now()]);
            }
        }

        if (isset($actions['restore'])) {
            foreach ($actions['restore'] as $conditions) {
                Rule::withTrashed()
                    ->where($conditions)
                    ->update(['deleted_at' => null]);
            }
        }
    }
}
