<?php

namespace Webkul\Barcode\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\Packaging;
use Webkul\Product\Models\Product;

class Transfers extends Component
{
    public OperationType $operationType;

    public string $search = '';

    public ?string $operationNotice = null;

    public string $operationNoticeColor = 'info';

    public array $matchingOperationIds = [];

    public function mount(OperationType $operationType): void
    {
        $this->operationType = $operationType;
    }

    public function openOperation()
    {
        $this->operationNotice = null;
        $this->operationNoticeColor = 'info';

        $operations = $this->findMatchingOperations($this->search);

        if ($operations->isEmpty()) {
            $this->matchingOperationIds = [];

            $this->operationNotice = __('barcode::app.operation-search.not-found');
            $this->operationNoticeColor = 'warning';
            $this->dispatchNativeFeedback($this->operationNotice);

            return null;
        }

        if ($operations->count() === 1) {
            $operation = $operations->first();

            return $this->redirectRoute('barcode.operation', [
                $operation->operationType,
                $operation,
                'scan' => $this->normalizeBarcode($this->search),
            ], navigate: true);
        }

        $this->matchingOperationIds = $operations->pluck('id')->all();

        $this->operationNotice = __('barcode::app.operation-search.multiple-found', ['count' => $operations->count()]);
        $this->operationNoticeColor = 'info';
        $this->dispatchNativeFeedback($this->operationNotice);

        return null;
    }

    public function updatedSearch(): void
    {
        if ($this->search === '') {
            $this->matchingOperationIds = [];

            $this->operationNotice = null;
            $this->operationNoticeColor = 'info';
        }
    }

    public function render(): View
    {
        $transfers = $this->matchingOperationIds !== []
            ? Operation::query()->with(['operationType', 'partner'])->whereIn('id', $this->matchingOperationIds)->get()
            : $this->transfers()
                ->filter(function ($operation): bool {
                    if ($this->search === '') {
                        return true;
                    }

                    $search = mb_strtolower($this->search);

                    return str_contains(mb_strtolower($operation->name), $search)
                        || str_contains(mb_strtolower((string) $operation->origin), $search)
                        || str_contains(mb_strtolower((string) $operation->partner?->name), $search);
                });

        return view('barcode::livewire.transfers', [
            'transfers' => $transfers,
        ])->layout('barcode::layouts.app', [
            'title' => $this->operationType->name,
        ]);
    }

    private function transfers(): Collection
    {
        return Operation::query()
            ->with(['partner', 'operationType'])
            ->where('operation_type_id', $this->operationType->id)
            ->whereNotIn('state', [
                OperationState::DRAFT->value,
                OperationState::DONE->value,
                OperationState::CANCELED->value,
            ])
            ->latest('scheduled_at')
            ->latest('id')
            ->limit(200)
            ->get();
    }

    private function findMatchingOperations(string $barcode): Collection
    {
        $barcode = $this->normalizeBarcode($barcode);

        if ($barcode === '') {
            return collect();
        }

        $operations = Operation::query()
            ->with('operationType')
            ->where('operation_type_id', $this->operationType->id)
            ->whereNotIn('state', [
                OperationState::DRAFT->value,
                OperationState::DONE->value,
                OperationState::CANCELED->value,
            ])
            ->where(function ($query) use ($barcode): void {
                $query->whereRaw('LOWER(name) = ?', [mb_strtolower($barcode)])
                    ->orWhereRaw('LOWER(origin) = ?', [mb_strtolower($barcode)])
                    ->orWhere('id', $barcode);
            })
            ->latest('scheduled_at')
            ->latest('id')
            ->get();

        if ($operations->isNotEmpty()) {
            return $operations;
        }

        return $this->findMatchingOperationsByInventory($barcode);
    }

    private function findMatchingOperationsByInventory(string $barcode): Collection
    {
        if ($product = $this->findProduct($barcode)) {
            return $this->findOperationsByMoveQuery(
                Move::query()->where('product_id', $product->id)
            );
        }

        if ($packaging = $this->findPackaging($barcode)) {
            return $this->findOperationsByMoveQuery(
                Move::query()
                    ->where('product_packaging_id', $packaging->id)
                    ->orWhere('product_id', $packaging->product_id)
            );
        }

        if ($lot = $this->findLot($barcode)) {
            return $this->findOperationsByMoveLineQuery(
                MoveLine::query()->where('lot_id', $lot->id)
            );
        }

        if ($package = $this->findPackage($barcode)) {
            return $this->findOperationsByMoveLineQuery(
                MoveLine::query()
                    ->where('package_id', $package->id)
                    ->orWhere('result_package_id', $package->id)
            );
        }

        return collect();
    }

    private function findOperationsByMoveQuery($moveQuery): Collection
    {
        return Operation::query()
            ->with('operationType')
            ->where('operation_type_id', $this->operationType->id)
            ->whereNotIn('state', [
                OperationState::DRAFT->value,
                OperationState::DONE->value,
                OperationState::CANCELED->value,
            ])
            ->whereIn('id', $moveQuery->select('operation_id'))
            ->latest('scheduled_at')
            ->latest('id')
            ->get();
    }

    private function findOperationsByMoveLineQuery($moveLineQuery): Collection
    {
        return Operation::query()
            ->with('operationType')
            ->where('operation_type_id', $this->operationType->id)
            ->whereNotIn('state', [
                OperationState::DRAFT->value,
                OperationState::DONE->value,
                OperationState::CANCELED->value,
            ])
            ->whereIn('id', $moveLineQuery->select('operation_id'))
            ->latest('scheduled_at')
            ->latest('id')
            ->get();
    }

    private function findProduct(string $barcode): ?Product
    {
        return Product::query()
            ->where('barcode', $barcode)
            ->orWhere('reference', $barcode)
            ->first();
    }

    private function findPackaging(string $barcode): ?Packaging
    {
        return Packaging::query()
            ->where('barcode', $barcode)
            ->first();
    }

    private function findLot(string $barcode): ?Lot
    {
        return Lot::query()
            ->where('name', $barcode)
            ->orWhere('reference', $barcode)
            ->first();
    }

    private function findPackage(string $barcode): ?Package
    {
        return Package::query()
            ->where('name', $barcode)
            ->first();
    }

    private function normalizeBarcode(string $barcode): string
    {
        $barcode = trim($barcode);
        $barcode = preg_replace('/\s+/', ' ', $barcode) ?: '';
        $barcode = preg_replace('/^packing\s+slip\s*/i', '', $barcode) ?: $barcode;

        return trim($barcode, " \t\n\r\0\x0B#");
    }

    private function dispatchNativeFeedback(?string $message, bool $vibrate = false, string $duration = 'short'): void
    {
        if (! $message) {
            return;
        }

        $this->dispatch('barcode-native-feedback', message: $message, vibrate: $vibrate, duration: $duration);
    }
}
