<?php

namespace Webkul\Barcode\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Webkul\Barcode\Support\OperationTypes;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;

class Dashboard extends Component
{
    public function render(): View
    {
        return view('barcode::livewire.dashboard', [
            'operationTypes' => $this->operationTypes(),
        ])->layout('barcode::layouts.app', [
            'title' => __('barcode::app.title'),
        ]);
    }

    private function operationTypes(): Collection
    {
        return OperationType::query()
            ->with('warehouse:id,name')
            ->select('inventories_operation_types.*')
            ->selectSub(
                Operation::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('inventories_operations.operation_type_id', 'inventories_operation_types.id')
                    ->whereNotIn('state', [
                        OperationState::DRAFT->value,
                        OperationState::DONE->value,
                        OperationState::CANCELED->value,
                    ]),
                'waiting_count'
            )
            ->orderBy('sort')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (OperationType $operationType): string => OperationTypes::groupKey($operationType))
            ->map(function (Collection $operationTypes) {
                $primaryOperationType = $operationTypes->first();
                $primaryOperationType->waiting_count = $operationTypes->sum('waiting_count');

                return $primaryOperationType;
            })
            ->values();
    }
}
