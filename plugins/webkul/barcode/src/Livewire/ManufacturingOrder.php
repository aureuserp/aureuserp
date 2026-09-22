<?php

namespace Webkul\Barcode\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Livewire\Component;
use Throwable;
use Webkul\Barcode\Support\Barcode;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Packaging;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Facades\Manufacturing;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;
use Webkul\Product\Models\Product;

class ManufacturingOrder extends Component
{
    public Order $order;

    public string $barcode = '';

    public ?string $notice = null;

    public string $noticeColor = 'info';

    public float|string|null $quantityProducing = null;

    public array $componentQuantities = [];

    public array $countedComponentIds = [];

    public ?int $selectedComponentId = null;

    public ?int $editingComponentId = null;

    public float|string|null $editingComponentQuantity = null;

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->quantityProducing = (float) $order->quantity_producing;

        $this->syncComponentState();

        if (request()->filled('scan')) {
            $this->resolveScan((string) request('scan'));
        }
    }

    public function scan(): void
    {
        $this->resolveScan($this->barcode);

        $this->barcode = '';
    }

    public function adjustQuantityProducing(float $amount): void
    {
        $this->quantityProducing = max(0, (float) $this->quantityProducing + $amount);
    }

    public function setQuantityProducing(float $quantity): void
    {
        $this->quantityProducing = max(0, $quantity);
    }

    public function adjustComponentQuantity(int $moveId, float $amount): void
    {
        $move = $this->componentMove($moveId);

        $this->applyComponentQuantity($move, (float) ($this->componentQuantities[$moveId] ?? 0) + $amount);
    }

    public function setComponentQuantity(int $moveId, float $quantity): void
    {
        $move = $this->componentMove($moveId);

        $this->applyComponentQuantity($move, $quantity);
    }

    public function editComponent(int $moveId): void
    {
        $move = $this->componentMove($moveId);

        $this->editingComponentId = $move->id;
        $this->selectedComponentId = $move->id;
        $this->editingComponentQuantity = (float) ($this->componentQuantities[$move->id] ?? 0);
    }

    public function discardComponentEdit(): void
    {
        $this->editingComponentId = null;
        $this->editingComponentQuantity = null;
    }

    public function setEditingComponentQuantity(float $quantity): void
    {
        if (! $this->editingComponentId) {
            return;
        }

        $this->editingComponentQuantity = max(0, $quantity);
    }

    public function adjustEditingComponentQuantity(float $amount): void
    {
        if (! $this->editingComponentId) {
            return;
        }

        $this->editingComponentQuantity = max(0, (float) $this->editingComponentQuantity + $amount);
    }

    public function confirmComponentEdit(): void
    {
        if (! $this->editingComponentId) {
            return;
        }

        $move = $this->componentMove($this->editingComponentId);

        $this->applyComponentQuantity($move, (float) $this->editingComponentQuantity);

        $this->notice = __('barcode::app.manufacturing.component-counted');
        $this->noticeColor = 'success';
        $this->dispatchNativeFeedback($this->notice, true);

        $this->discardComponentEdit();
    }

    public function produceAll(): void
    {
        $this->quantityProducing = $this->remainingQuantity();

        foreach ($this->componentMoves() as $move) {
            $this->applyComponentQuantity($move, $this->componentDemand($move));
        }

        $this->notice = __('barcode::app.manufacturing.produce-all-applied');
        $this->noticeColor = 'success';
        $this->dispatchNativeFeedback($this->notice, true);
    }

    public function executeAction(string $action): void
    {
        try {
            DB::transaction(function () use ($action): void {
                $this->runAction($action);
            });

            $this->order->refresh();

            if (in_array($action, ['done', 'cancel'], true)) {
                $this->componentQuantities = [];
                $this->countedComponentIds = [];
                $this->quantityProducing = (float) $this->order->quantity_producing;
            } else {
                $this->reseedUntouchedComponents();
            }

            $this->syncComponentState();

            $this->notice = __('barcode::app.actions.completed');
            $this->noticeColor = 'success';
            $this->dispatchNativeFeedback($this->notice, true);

            if (in_array($action, ['done', 'cancel'], true)) {
                $this->redirectRoute('barcode.manufacturing-orders', navigate: true);
            }
        } catch (Throwable $e) {
            $this->notice = $e->getMessage();
            $this->noticeColor = 'danger';
            $this->dispatchNativeFeedback($this->notice, false, 'long');
        }
    }

    public function render(): View
    {
        $order = $this->loadOrder();

        $this->syncComponentState();

        return view('barcode::livewire.manufacturing-order', [
            'order'             => $order,
            'components'        => $this->filteredComponentMoves(),
            'editingComponent'  => $this->editingComponentId ? $this->componentMoves()->firstWhere('id', $this->editingComponentId) : null,
            'remainingQuantity' => $this->remainingQuantity(),
            'actions'           => $this->availableActions(),
            'blockedNotice'     => $this->blockedNotice(),
        ])->layout('barcode::layouts.app', [
            'title' => $order->name,
        ]);
    }

    public function componentDemand(Move $move): float
    {
        return (float) $move->product_uom_qty;
    }

    private function runAction(string $action): void
    {
        if ($this->availableActions() === []) {
            throw new InvalidArgumentException($this->blockedNotice() ?? __('barcode::app.actions.unsupported'));
        }

        match ($action) {
            'confirm' => Manufacturing::confirmManufacturingOrder($this->order),
            'start'   => Manufacturing::startManufacturingOrder($this->order),
            'cancel'  => Manufacturing::cancelManufacturingOrder($this->order),
            'done'    => $this->completeOrder(),
            default   => throw new InvalidArgumentException(__('barcode::app.actions.unsupported')),
        };
    }

    private function completeOrder(): void
    {
        $order = $this->order;

        $order->update(['quantity_producing' => max(0, (float) $this->quantityProducing)]);

        $order->refresh();

        $order->checkSnUniqueness();

        if (float_is_zero($order->quantity_producing, precisionRounding: $order->uom->rounding) && $order->autoProductionChecks()) {
            $order->setQuantities();

            $order->refresh();
        }

        $this->persistComponentQuantities($order);

        Manufacturing::doneManufacturingOrder($order->refresh());
    }

    private function persistComponentQuantities(Order $order): void
    {
        foreach ($order->rawMaterialMoves()->get() as $move) {
            if (! ($this->countedComponentIds[$move->id] ?? false)) {
                continue;
            }

            if (in_array($move->state, [MoveState::DONE, MoveState::CANCELED], true)) {
                continue;
            }

            $quantity = max(0, (float) ($this->componentQuantities[$move->id] ?? 0));

            $move->update([
                'quantity'  => $quantity,
                'is_picked' => $quantity > 0,
            ]);
        }
    }

    private function applyComponentQuantity(Move $move, float $quantity): void
    {
        $this->componentQuantities[$move->id] = max(0, $quantity);
        $this->countedComponentIds[$move->id] = true;
        $this->selectedComponentId = $this->componentQuantities[$move->id] > 0 ? $move->id : null;
    }

    private function componentMove(int $moveId): Move
    {
        $move = $this->componentMoves()->firstWhere('id', $moveId);

        abort_unless((bool) $move, 404);

        return $move;
    }

    private function componentMoves(): Collection
    {
        return $this->loadOrder()->rawMaterialMoves
            ->filter(fn (Move $move): bool => ! in_array($move->state, [MoveState::CANCELED], true))
            ->values();
    }

    private function filteredComponentMoves(): Collection
    {
        $components = $this->componentMoves();

        if ($this->barcode === '') {
            return $components;
        }

        $search = mb_strtolower($this->barcode);

        return $components->filter(function (Move $move) use ($search): bool {
            return str_contains(mb_strtolower((string) $move->product?->name), $search)
                || str_contains(mb_strtolower((string) $move->product?->reference), $search)
                || str_contains(mb_strtolower((string) $move->product?->barcode), $search);
        })->values();
    }

    private function reseedUntouchedComponents(): void
    {
        foreach ($this->componentMoves() as $move) {
            if ($this->countedComponentIds[$move->id] ?? false) {
                continue;
            }

            $this->componentQuantities[$move->id] = (float) $move->quantity;
        }
    }

    private function unavailableProductComponents(): Collection
    {
        return $this->componentMoves()->filter(fn (Move $move): bool => $move->product === null);
    }

    private function blockedNotice(): ?string
    {
        if ($this->availableActionsForState() === []) {
            return null;
        }

        $unavailable = $this->unavailableProductComponents();

        if ($unavailable->isNotEmpty()) {
            return __('barcode::app.actions.product-unavailable', ['count' => $unavailable->count()]);
        }

        if ($this->order->product === null) {
            return __('barcode::app.actions.product-unavailable', ['count' => 1]);
        }

        return null;
    }

    private function syncComponentState(): void
    {
        foreach ($this->componentMoves() as $move) {
            $this->componentQuantities[$move->id] ??= (float) $move->quantity;
        }
    }

    private function remainingQuantity(): float
    {
        return max(0, (float) $this->order->quantity - (float) $this->order->quantity_produced);
    }

    private function loadOrder(): Order
    {
        return $this->order->loadMissing([
            'product',
            'uom',
            'billOfMaterial',
            'producingLot',
            'sourceLocation',
            'destinationLocation',
            'rawMaterialMoves.product',
            'rawMaterialMoves.uom',
        ]);
    }

    private function availableActions(): array
    {
        if ($this->blockedNotice() !== null) {
            return [];
        }

        return $this->availableActionsForState();
    }

    private function availableActionsForState(): array
    {
        return match ($this->order->state) {
            ManufacturingOrderState::DRAFT => [
                ['key' => 'confirm', 'label' => __('barcode::app.manufacturing.confirm'), 'variant' => 'primary'],
                ['key' => 'cancel', 'label' => __('barcode::app.actions.cancel'), 'variant' => 'danger'],
            ],
            ManufacturingOrderState::CONFIRMED => [
                ['key' => 'start', 'label' => __('barcode::app.manufacturing.start'), 'variant' => 'gray'],
                ['key' => 'done', 'label' => __('barcode::app.manufacturing.done'), 'variant' => 'primary'],
                ['key' => 'cancel', 'label' => __('barcode::app.actions.cancel'), 'variant' => 'danger'],
            ],
            ManufacturingOrderState::PROGRESS, ManufacturingOrderState::TO_CLOSE => [
                ['key' => 'done', 'label' => __('barcode::app.manufacturing.done'), 'variant' => 'primary'],
                ['key' => 'cancel', 'label' => __('barcode::app.actions.cancel'), 'variant' => 'danger'],
            ],
            default => [],
        };
    }

    private function resolveScan(string $barcode): void
    {
        $barcode = Barcode::normalize($barcode);

        if ($barcode === '') {
            $this->notice = __('barcode::app.scan.empty');
            $this->noticeColor = 'warning';

            return;
        }

        if (! $product = $this->resolveScannedProduct($barcode)) {
            $this->notice = __('barcode::app.scan.not-found');
            $this->noticeColor = 'warning';
            $this->dispatchNativeFeedback($this->notice, false);

            return;
        }

        if ((int) $product->id === (int) $this->order->product_id) {
            $this->quantityProducing = min($this->remainingQuantity(), (float) $this->quantityProducing + 1);

            $this->notice = __('barcode::app.manufacturing.finished-scanned');
            $this->noticeColor = 'success';
            $this->dispatchNativeFeedback($this->notice, true);

            return;
        }

        $move = $this->componentMoves()->firstWhere('product_id', $product->id);

        if (! $move) {
            $this->notice = __('barcode::app.manufacturing.component-not-on-order');
            $this->noticeColor = 'warning';
            $this->dispatchNativeFeedback($this->notice, false);

            return;
        }

        $this->applyComponentQuantity($move, (float) ($this->componentQuantities[$move->id] ?? 0) + 1);

        $this->notice = __('barcode::app.manufacturing.component-scanned');
        $this->noticeColor = 'success';
        $this->dispatchNativeFeedback($this->notice, true);
        $this->dispatch('barcode-record-located', targetId: 'component-'.$move->id, locatedAt: now()->getTimestampMs());
    }

    private function resolveScannedProduct(string $barcode): ?Product
    {
        if ($product = $this->findProduct($barcode)) {
            return $product;
        }

        if ($packaging = Packaging::query()->with('product')->whereRaw(db_dialect()->caseInsensitiveEquals('barcode'), [$barcode])->first()) {
            return $packaging->product;
        }

        $lot = Lot::query()
            ->with('product')
            ->whereRaw(db_dialect()->caseInsensitiveEquals('name'), [$barcode])
            ->orWhereRaw(db_dialect()->caseInsensitiveEquals('reference'), [$barcode])
            ->first();

        return $lot?->product;
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
