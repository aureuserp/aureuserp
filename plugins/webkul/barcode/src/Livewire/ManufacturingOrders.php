<?php

namespace Webkul\Barcode\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Webkul\Barcode\Support\Barcode;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Models\Order;
use Webkul\Product\Models\Product;

class ManufacturingOrders extends Component
{
    public string $search = '';

    public ?string $notice = null;

    public string $noticeColor = 'info';

    public array $matchingOrderIds = [];

    public int $perPage = 50;

    public function openOrder()
    {
        $this->notice = null;
        $this->noticeColor = 'info';

        $orders = $this->findMatchingOrders($this->search);

        if ($orders->isEmpty()) {
            $this->matchingOrderIds = [];

            $this->notice = __('barcode::app.manufacturing.not-found');
            $this->noticeColor = 'warning';
            $this->dispatchNativeFeedback($this->notice);

            return null;
        }

        if ($orders->count() === 1) {
            return $this->redirectRoute('barcode.manufacturing-order', [
                'order' => $orders->first(),
                'scan'  => Barcode::normalize($this->search),
            ], navigate: true);
        }

        $this->matchingOrderIds = $orders->pluck('id')->all();

        $this->notice = __('barcode::app.manufacturing.multiple-found', ['count' => $orders->count()]);
        $this->noticeColor = 'info';
        $this->dispatchNativeFeedback($this->notice);

        return null;
    }

    public function loadMore(): void
    {
        $this->perPage += 50;
    }

    public function updatedSearch(): void
    {
        if ($this->search === '') {
            $this->matchingOrderIds = [];

            $this->notice = null;
            $this->noticeColor = 'info';
        }
    }

    public function render(): View
    {
        if ($this->matchingOrderIds !== []) {
            $orders = $this->baseQuery()->whereIn('id', $this->matchingOrderIds)->get();
            $totalOrders = $orders->count();
        } else {
            $orders = $this->openOrdersQuery()->limit($this->perPage)->get();
            $totalOrders = $this->openOrdersQuery()->count();
        }

        return view('barcode::livewire.manufacturing-orders', [
            'orders'      => $orders,
            'totalOrders' => $totalOrders,
        ])->layout('barcode::layouts.app', [
            'title' => __('barcode::app.manufacturing.title'),
        ]);
    }

    private function baseQuery(): Builder
    {
        return Order::query()
            ->with(['product', 'uom', 'billOfMaterial'])
            ->orderByRaw('deadline_at is null')
            ->orderBy('deadline_at')
            ->orderBy('id');
    }

    private function openOrdersQuery(): Builder
    {
        return $this->baseQuery()
            ->whereNotIn('state', [
                ManufacturingOrderState::DONE->value,
                ManufacturingOrderState::CANCEL->value,
            ])
            ->when($this->search !== '', function (Builder $query): void {
                $search = mb_strtolower($this->search);

                $query->where(function (Builder $searchQuery) use ($search): void {
                    $searchQuery
                        ->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(reference) like ?', ["%{$search}%"])
                        ->orWhereHas('product', function (Builder $productQuery) use ($search): void {
                            $productQuery
                                ->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(reference) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(barcode) like ?', ["%{$search}%"]);
                        });
                });
            });
    }

    private function findMatchingOrders(string $barcode): Collection
    {
        $barcode = Barcode::normalize($barcode);

        if ($barcode === '') {
            return collect();
        }

        $orders = $this->baseQuery()
            ->whereNotIn('state', [
                ManufacturingOrderState::DONE->value,
                ManufacturingOrderState::CANCEL->value,
            ])
            ->where(function (Builder $query) use ($barcode): void {
                $query->whereRaw(db_dialect()->caseInsensitiveEquals('name'), [$barcode])
                    ->orWhereRaw(db_dialect()->caseInsensitiveEquals('reference'), [$barcode]);
            })
            ->get();

        if ($orders->isNotEmpty()) {
            return $orders;
        }

        if (! $product = $this->findProduct($barcode)) {
            return collect();
        }

        return $this->baseQuery()
            ->whereNotIn('state', [
                ManufacturingOrderState::DONE->value,
                ManufacturingOrderState::CANCEL->value,
            ])
            ->where('product_id', $product->id)
            ->get();
    }

    private function findProduct(string $barcode): ?Product
    {
        return Product::query()
            ->whereRaw(db_dialect()->caseInsensitiveEquals('barcode'), [$barcode])
            ->orWhereRaw(db_dialect()->caseInsensitiveEquals('reference'), [$barcode])
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
