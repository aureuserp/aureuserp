<?php

namespace Webkul\Manufacturing;

use Illuminate\Support\Collection;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Services\KitExpander;
use Webkul\Manufacturing\Services\OrderWorkflow;
use Webkul\Manufacturing\Services\ProductionRecorder;

class ManufacturingManager
{
    public function __construct(
        protected OrderWorkflow $workflow,
        protected ProductionRecorder $production,
        protected KitExpander $kits,
    ) {}

    public function confirmManufacturingOrder(Order $order): Order
    {
        return $this->workflow->confirm($order);
    }

    public function startManufacturingOrder(Order $order): Order
    {
        return $this->workflow->start($order);
    }

    public function planManufacturingOrder(Order $order): Order
    {
        return $this->workflow->plan($order);
    }

    public function unplanManufacturingOrder(Order $order): Order
    {
        return $this->workflow->unplan($order);
    }

    public function doneManufacturingOrder(Order $order): void
    {
        $this->workflow->complete($order);
    }

    public function cancelManufacturingOrder(Order $order): Order
    {
        return $this->workflow->cancel($order);
    }

    public function confirmMoves(Collection $moves, bool $merge = false, ?Collection $mergeInto = null): void
    {
        $this->workflow->confirmMoves($moves, $merge, $mergeInto);
    }

    public function planWorkOrders(Order $order, bool $replan = false): Order
    {
        return $this->workflow->scheduleWorkOrders($order, $replan);
    }

    public function settleProduction(Order $order, bool $cancelBackorder = false): bool
    {
        return $this->production->record($order, $cancelBackorder);
    }

    public function expandKitMoves(Collection $moves): Collection
    {
        return $this->kits->expandMoves($moves);
    }
}
