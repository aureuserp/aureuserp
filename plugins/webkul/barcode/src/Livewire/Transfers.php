<?php

namespace Webkul\Barcode\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Webkul\Barcode\Support\Barcode;
use Webkul\Barcode\Support\OperationTypes;
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

    public int $perPage = 50;

    private ?array $operationTypeIds = null;

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
                'scan' => Barcode::normalize($this->search),
            ], navigate: true);
        }

        $this->matchingOperationIds = $operations->pluck('id')->all();

        $this->operationNotice = __('barcode::app.operation-search.multiple-found', ['count' => $operations->count()]);
        $this->operationNoticeColor = 'info';
        $this->dispatchNativeFeedback($this->operationNotice);

        return null;
    }

    public function loadMore(): void
    {
        $this->perPage += 50;
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
        if ($this->matchingOperationIds !== []) {
            $transfers = Operation::query()
                ->with(['operationType', 'partner'])
                ->whereIn('id', $this->matchingOperationIds)
                ->latest('scheduled_at')
                ->latest('id')
                ->get();

            $totalTransfers = $transfers->count();
        } else {
            $transfers = $this->transfersQuery()->limit($this->perPage)->get();
            $totalTransfers = $this->transfersQuery()->count();
        }

        return view('barcode::livewire.transfers', [
            'transfers'      => $transfers,
            'totalTransfers' => $totalTransfers,
        ])->layout('barcode::layouts.app', [
            'title' => $this->operationType->name,
        ]);
    }

    private function operationTypeIds(): array
    {
        return $this->operationTypeIds ??= OperationTypes::siblingIds($this->operationType);
    }

    private function transfersQuery(): Builder
    {
        return Operation::query()
            ->with(['partner', 'operationType'])
            ->whereIn('operation_type_id', $this->operationTypeIds())
            ->whereNotIn('state', [
                OperationState::DRAFT->value,
                OperationState::DONE->value,
                OperationState::CANCELED->value,
            ])
            ->when($this->search !== '', function (Builder $query): void {
                $search = mb_strtolower($this->search);

                $query->where(function (Builder $searchQuery) use ($search): void {
                    $searchQuery
                        ->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(origin) like ?', ["%{$search}%"])
                        ->orWhereHas('partner', function (Builder $partnerQuery) use ($search): void {
                            $partnerQuery->whereRaw('LOWER(name) like ?', ["%{$search}%"]);
                        });
                });
            })
            ->latest('scheduled_at')
            ->latest('id');
    }

    private function findMatchingOperations(string $barcode): Collection
    {
        $barcode = Barcode::normalize($barcode);

        if ($barcode === '') {
            return collect();
        }

        $operations = Operation::query()
            ->with('operationType')
            ->whereIn('operation_type_id', $this->operationTypeIds())
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
            ->whereIn('operation_type_id', $this->operationTypeIds())
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
            ->whereIn('operation_type_id', $this->operationTypeIds())
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
            ->whereRaw(db_dialect()->caseInsensitiveEquals('barcode'), [$barcode])
            ->orWhereRaw(db_dialect()->caseInsensitiveEquals('reference'), [$barcode])
            ->first();
    }

    private function findPackaging(string $barcode): ?Packaging
    {
        return Packaging::query()
            ->whereRaw(db_dialect()->caseInsensitiveEquals('barcode'), [$barcode])
            ->first();
    }

    private function findLot(string $barcode): ?Lot
    {
        return Lot::query()
            ->whereRaw(db_dialect()->caseInsensitiveEquals('name'), [$barcode])
            ->orWhereRaw(db_dialect()->caseInsensitiveEquals('reference'), [$barcode])
            ->first();
    }

    private function findPackage(string $barcode): ?Package
    {
        return Package::query()
            ->whereRaw(db_dialect()->caseInsensitiveEquals('name'), [$barcode])
            ->first();
    }

    private function dispatchNativeFeedback(?string $message, bool $vibrate = false, string $duration = 'short'): void
    {
        if (! $message) {
            return;
        }

        $this->dispatch('barcode-native-feedback', message: $message, vibrate: $vibrate, duration: $duration);
    }
}
