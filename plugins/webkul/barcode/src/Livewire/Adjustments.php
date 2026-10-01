<?php

namespace Webkul\Barcode\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Webkul\Barcode\Support\Barcode;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;

class Adjustments extends Component
{
    public string $search = '';

    public ?string $notice = null;

    public string $noticeColor = 'info';

    public ?int $selectedQuantityId = null;

    public ?int $selectedLocationId = null;

    public ?int $selectedProductId = null;

    public ?int $selectedLotId = null;

    public ?int $editingQuantityId = null;

    public float|string|null $editingCountedQuantity = null;

    public int $perPage = 50;

    public function scan(): void
    {
        $barcode = Barcode::normalize($this->search);

        if ($barcode === '') {
            $this->notice = __('barcode::app.scan.empty');
            $this->noticeColor = 'warning';

            return;
        }

        if ($location = $this->findLocation($barcode)) {
            $this->selectedLocationId = $location->id;
            $this->selectedProductId = null;
            $this->selectedLotId = null;
            $this->selectedQuantityId = null;
            $this->search = '';
            $this->notice = __('barcode::app.adjustments.location-scanned', ['location' => $location->full_name ?: $location->name]);
            $this->noticeColor = 'info';
            $this->dispatchNativeFeedback($this->notice, true);

            return;
        }

        if ($product = $this->findProduct($barcode)) {
            $this->selectedProductId = $product->id;
            $this->selectedLotId = null;
            $this->search = '';

            $matchingQuantities = $this->inventoryQuery()
                ->where('product_id', $product->id)
                ->get();

            if ($matchingQuantities->isEmpty()) {
                $this->notice = __('barcode::app.adjustments.product-not-found');
                $this->noticeColor = 'warning';
                $this->dispatchNativeFeedback($this->notice, false);

                return;
            }

            $this->handleMatchedQuantities($matchingQuantities, __('barcode::app.scan.move-located'));

            return;
        }

        if ($lot = $this->findLot($barcode)) {
            $this->selectedLotId = $lot->id;
            $this->search = '';

            $matchingQuantities = $this->inventoryQuery()
                ->where('lot_id', $lot->id)
                ->get();

            if ($matchingQuantities->isEmpty()) {
                $this->notice = __('barcode::app.adjustments.lot-not-found');
                $this->noticeColor = 'warning';
                $this->dispatchNativeFeedback($this->notice, false);

                return;
            }

            $this->handleMatchedQuantities($matchingQuantities, __('barcode::app.scan.move-located'));

            return;
        }

        $this->notice = __('barcode::app.scan.not-found');
        $this->noticeColor = 'warning';
        $this->dispatchNativeFeedback($this->notice, false);
    }

    public function loadMore(): void
    {
        $this->perPage += 50;
    }

    public function clearFilters(): void
    {
        $this->selectedLocationId = null;
        $this->selectedProductId = null;
        $this->selectedLotId = null;
        $this->selectedQuantityId = null;
        $this->notice = __('barcode::app.adjustments.location-cleared');
        $this->noticeColor = 'info';
    }

    public function quickCountQuantity(int $quantityId): void
    {
        if (! $quantity = $this->quantityRecord($quantityId)) {
            return;
        }

        $onHandQuantity = (float) $quantity->quantity;

        $this->persistCountedQuantity($quantity, $onHandQuantity);

        $this->selectedQuantityId = $quantityId;
        $this->notice = __('barcode::app.adjustments.count-set', [
            'quantity' => $this->formatQuantity($onHandQuantity),
            'uom'      => $quantity->product?->uom?->name,
        ]);
        $this->noticeColor = 'success';
        $this->dispatchNativeFeedback($this->notice, true);
    }

    public function adjustQuantityCount(int $quantityId, float $amount): void
    {
        if (! $quantity = $this->quantityRecord($quantityId)) {
            return;
        }

        $currentCounted = $quantity->inventory_quantity_set ? (float) $quantity->counted_quantity : 0.0;
        $newCounted = max(0, $currentCounted + $amount);

        $this->persistCountedQuantity($quantity, $newCounted);

        $this->selectedQuantityId = $quantityId;
        $this->notice = __('barcode::app.adjustments.count-saved', [
            'quantity'   => $this->formatQuantity($newCounted),
            'uom'        => $quantity->product?->uom?->name,
            'difference' => $this->formatDifference($newCounted - (float) $quantity->quantity),
        ]);
        $this->noticeColor = 'success';
    }

    public function editQuantity(int $quantityId): void
    {
        if (! $quantity = $this->quantityRecord($quantityId)) {
            return;
        }

        $this->editingQuantityId = $quantityId;
        $this->selectedQuantityId = $quantityId;
        $this->editingCountedQuantity = $quantity->inventory_quantity_set
            ? (float) $quantity->counted_quantity
            : 0.0;
    }

    public function setEditingQuantity(float $quantity): void
    {
        if (! $this->editingQuantityId) {
            return;
        }

        $this->editingCountedQuantity = max(0, $quantity);
    }

    public function adjustEditingQuantity(float $amount): void
    {
        if (! $this->editingQuantityId) {
            return;
        }

        $this->editingCountedQuantity = max(0, (float) $this->editingCountedQuantity + $amount);
    }

    public function discardQuantityEdit(): void
    {
        $this->editingQuantityId = null;
        $this->editingCountedQuantity = null;
    }

    public function confirmQuantityEdit(): void
    {
        if (! $this->editingQuantityId) {
            return;
        }

        if (! $quantity = $this->quantityRecord($this->editingQuantityId)) {
            $this->discardQuantityEdit();

            return;
        }

        $countedQuantity = max(0, (float) $this->editingCountedQuantity);
        $difference = $countedQuantity - (float) $quantity->quantity;

        $this->persistCountedQuantity($quantity, $countedQuantity);

        $this->notice = __('barcode::app.adjustments.count-saved', [
            'quantity'   => $this->formatQuantity($countedQuantity),
            'uom'        => $quantity->product?->uom?->name,
            'difference' => $this->formatDifference($difference),
        ]);
        $this->noticeColor = 'success';
        $this->dispatchNativeFeedback($this->notice, true);

        $this->discardQuantityEdit();
    }

    public function clearQuantityCount(int $quantityId): void
    {
        if (! $quantity = $this->quantityRecord($quantityId)) {
            return;
        }

        $quantity->update([
            'counted_quantity'        => 0,
            'inventory_quantity_set'  => false,
            'inventory_diff_quantity' => 0,
        ]);

        $this->selectedQuantityId = $quantityId;
        $this->notice = __('barcode::app.adjustments.count-cleared');
        $this->noticeColor = 'info';
    }

    public function applyQuantityCount(int $quantityId): void
    {
        if (! $quantity = $this->quantityRecord($quantityId)) {
            return;
        }

        if (! $quantity->inventory_quantity_set) {
            return;
        }

        $countedQuantity = (float) $quantity->counted_quantity;
        $difference = $countedQuantity - (float) $quantity->quantity;
        $uomName = $quantity->product?->uom?->name;

        $quantity->update([
            'quantity'                => $countedQuantity,
            'counted_quantity'        => 0,
            'inventory_quantity_set'  => false,
        ]);

        $this->selectedQuantityId = $quantityId;

        if ($this->isUnchanged($quantity, $difference)) {
            $this->notice = __('barcode::app.adjustments.count-unchanged', [
                'quantity' => $this->formatQuantity($countedQuantity),
                'uom'      => $uomName,
            ]);
            $this->noticeColor = 'info';
        } else {
            $this->notice = __('barcode::app.adjustments.count-applied', [
                'quantity'   => $this->formatQuantity($countedQuantity),
                'uom'        => $uomName,
                'difference' => $this->formatDifference($difference),
            ]);
            $this->noticeColor = 'success';
        }

        $this->dispatchNativeFeedback($this->notice, true);
    }

    public function render(): View
    {
        $editingQuantity = $this->editingQuantityId
            ? ProductQuantity::query()
                ->with(['company', 'location', 'lot', 'package', 'product.uom'])
                ->whereHas('product')
                ->find($this->editingQuantityId)
            : null;

        return view('barcode::livewire.adjustments', [
            'editingQuantity'  => $editingQuantity,
            'selectedLocation' => $this->selectedLocation(),
            'selectedProduct'  => $this->selectedProduct(),
            'selectedLot'      => $this->selectedLot(),
            'quantities'       => $this->loadQuantities(),
            'totalQuantities'  => $this->inventoryQuery()->count(),
        ])->layout('barcode::layouts.app', [
            'title' => __('barcode::app.adjustments.title'),
        ]);
    }

    private function inventoryQuery(): Builder
    {
        return ProductQuantity::query()
            ->with([
                'company',
                'location',
                'lot',
                'package',
                'product.uom',
            ])
            ->whereHas('location', function (Builder $query): void {
                $query->where('type', LocationType::INTERNAL)
                    ->where('is_scrap', false);
            })
            ->whereHas('product')
            ->when($this->selectedLocationId, fn (Builder $query) => $query->where('location_id', $this->selectedLocationId))
            ->when($this->selectedProductId, fn (Builder $query) => $query->where('product_id', $this->selectedProductId))
            ->when($this->selectedLotId, fn (Builder $query) => $query->where('lot_id', $this->selectedLotId))
            ->when($this->search !== '', function (Builder $query): void {
                $search = mb_strtolower($this->search);

                $query->where(function (Builder $searchQuery) use ($search): void {
                    $searchQuery
                        ->whereHas('location', function (Builder $locationQuery) use ($search): void {
                            $locationQuery
                                ->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(full_name) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(barcode) like ?', ["%{$search}%"]);
                        })
                        ->orWhereHas('product', function (Builder $productQuery) use ($search): void {
                            $productQuery
                                ->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(reference) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(barcode) like ?', ["%{$search}%"]);
                        })
                        ->orWhereHas('lot', function (Builder $lotQuery) use ($search): void {
                            $lotQuery
                                ->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(reference) like ?', ["%{$search}%"]);
                        });
                });
            })
            ->where(function (Builder $query): void {
                $query->where('quantity', '>', 0)
                    ->orWhere('inventory_quantity_set', true);
            })
            ->orderBy('location_id')
            ->orderBy('product_id')
            ->orderBy('lot_id')
            ->latest('id');
    }

    private function loadQuantities(): Collection
    {
        return $this->inventoryQuery()
            ->limit($this->perPage)
            ->get();
    }

    private function handleMatchedQuantities(Collection $matchingQuantities, string $message): void
    {
        if ($matchingQuantities->count() === 1) {
            $quantity = $matchingQuantities->first();

            $this->selectedQuantityId = $quantity->id;
            $this->notice = $message;
            $this->noticeColor = 'success';
            $this->dispatchNativeFeedback($this->notice, true);
            $this->dispatch('barcode-record-located', targetId: 'quantity-'.$quantity->id, locatedAt: now()->getTimestampMs());

            return;
        }

        $this->selectedQuantityId = null;
        $this->notice = __('barcode::app.adjustments.multiple-found', ['count' => $matchingQuantities->count()]);
        $this->noticeColor = 'info';
        $this->dispatchNativeFeedback($this->notice, true);
    }

    private function quantityRecord(int $quantityId): ?ProductQuantity
    {
        $quantity = ProductQuantity::query()
            ->with(['company', 'location', 'lot', 'package', 'product.uom'])
            ->whereHas('product')
            ->find($quantityId);

        if (! $quantity) {
            $this->notice = __('barcode::app.adjustments.product-unavailable');
            $this->noticeColor = 'warning';
            $this->dispatchNativeFeedback($this->notice, false, 'long');
        }

        return $quantity;
    }

    public function formatQuantity(float $quantity): string
    {
        return rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.') ?: '0';
    }

    public function formatDifference(float $difference): string
    {
        return ($difference > 0 ? '+' : '').$this->formatQuantity($difference);
    }

    private function isUnchanged(ProductQuantity $quantity, float $difference): bool
    {
        $rounding = (float) ($quantity->product?->uom?->rounding ?: 0.01);

        return float_is_zero($difference, precisionRounding: $rounding);
    }

    private function persistCountedQuantity(ProductQuantity $quantity, float $countedQuantity): void
    {
        $quantity->update([
            'counted_quantity'        => $countedQuantity,
            'inventory_quantity_set'  => true,
            'inventory_diff_quantity' => $countedQuantity - (float) $quantity->quantity,
        ]);
    }

    private function findLocation(string $barcode): ?Location
    {
        return Location::query()
            ->where('type', LocationType::INTERNAL)
            ->where('is_scrap', false)
            ->where(function (Builder $query) use ($barcode): void {
                $query->whereRaw(db_dialect()->caseInsensitiveEquals('barcode'), [$barcode])
                    ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($barcode)])
                    ->orWhereRaw('LOWER(full_name) = ?', [mb_strtolower($barcode)]);
            })
            ->first();
    }

    private function findProduct(string $barcode): ?Product
    {
        return Product::query()
            ->whereRaw(db_dialect()->caseInsensitiveEquals('barcode'), [$barcode])
            ->orWhereRaw(db_dialect()->caseInsensitiveEquals('reference'), [$barcode])
            ->first();
    }

    private function findLot(string $barcode): ?Lot
    {
        return Lot::query()
            ->whereRaw(db_dialect()->caseInsensitiveEquals('name'), [$barcode])
            ->orWhereRaw(db_dialect()->caseInsensitiveEquals('reference'), [$barcode])
            ->first();
    }

    private function selectedLocation(): ?Location
    {
        return $this->selectedLocationId ? Location::query()->find($this->selectedLocationId) : null;
    }

    private function selectedProduct(): ?Product
    {
        return $this->selectedProductId ? Product::query()->find($this->selectedProductId) : null;
    }

    private function selectedLot(): ?Lot
    {
        return $this->selectedLotId ? Lot::query()->find($this->selectedLotId) : null;
    }

    private function dispatchNativeFeedback(?string $message, bool $vibrate = false, string $duration = 'short'): void
    {
        if (! $message) {
            return;
        }

        $this->dispatch('barcode-native-feedback', message: $message, vibrate: $vibrate, duration: $duration);
    }
}
