<?php

namespace Webkul\PointOfSale;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Webkul\Account\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Warehouse as InventoryWarehouse;
use Webkul\PointOfSale\Models\CashMovement;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Floor;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Models\Warehouse;
use Webkul\PointOfSale\Services\BillSplitter;
use Webkul\PointOfSale\Services\FiscalPositionResolver;
use Webkul\PointOfSale\Services\FloorPlanEditor;
use Webkul\PointOfSale\Services\GlobalDiscountApplier;
use Webkul\PointOfSale\Services\OrderCalculator;
use Webkul\PointOfSale\Services\OrderProcessor;
use Webkul\PointOfSale\Services\OrderWorkflow;
use Webkul\PointOfSale\Services\PickingGenerator;
use Webkul\PointOfSale\Services\PosInvoicer;
use Webkul\PointOfSale\Services\PreparationRouter;
use Webkul\PointOfSale\Services\PriceResolver;
use Webkul\PointOfSale\Services\RefundProcessor;
use Webkul\PointOfSale\Services\SessionCloser;
use Webkul\PointOfSale\Services\SessionPickingGenerator;
use Webkul\PointOfSale\Services\SessionPreflight;
use Webkul\PointOfSale\Services\SessionWorkflow;
use Webkul\PointOfSale\Services\ShipLaterProcurementRequester;
use Webkul\PointOfSale\Services\WarehouseProvisioner;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\Product;

class PointOfSaleManager
{
    public function __construct(
        protected BillSplitter $billSplitter,
        protected OrderCalculator $calculator,
        protected FiscalPositionResolver $fiscalPositions,
        protected GlobalDiscountApplier $globalDiscount,
        protected OrderProcessor $processor,
        protected OrderWorkflow $orders,
        protected PickingGenerator $pickings,
        protected PreparationRouter $preparation,
        protected PosInvoicer $invoicer,
        protected PriceResolver $prices,
        protected RefundProcessor $refunds,
        protected SessionCloser $sessionCloser,
        protected SessionPreflight $preflight,
        protected SessionPickingGenerator $sessionPickings,
        protected SessionWorkflow $sessions,
        protected ShipLaterProcurementRequester $shipLater,
        protected WarehouseProvisioner $warehouses,
    ) {}

    public function syncOrder(array $payload): Order
    {
        return $this->processor->process($payload);
    }

    public function syncOrders(array $payloads): array
    {
        return $this->processor->processBatch($payloads);
    }

    public function payOrder(Order $order): Order
    {
        return $this->orders->markPaid($order);
    }

    public function completeOrder(Order $order): Order
    {
        return $this->orders->markDone($order);
    }

    public function splitOrder(Order $order, array $lineQuantities): Order
    {
        return $this->billSplitter->split($order, $lineQuantities);
    }

    public function applyGlobalDiscount(Order $order, float $percentage): Order
    {
        return $this->globalDiscount->apply($order, $percentage);
    }

    public function removeGlobalDiscount(Order $order): Order
    {
        return $this->globalDiscount->remove($order);
    }

    public function applyFiscalPosition(Order $order): Order
    {
        return $this->fiscalPositions->applyTo($order);
    }

    public function routePreparationTickets(Order $order): Collection
    {
        return $this->preparation->route($order);
    }

    public function addOrderTip(Order $order, float $amount): Order
    {
        return $this->orders->addTip($order, $amount);
    }

    public function cancelOrder(Order $order): Order
    {
        return $this->orders->cancel($order);
    }

    public function recomputeOrder(Order $order): Order
    {
        return $this->calculator->recompute($order);
    }

    public function generateOrderPicking(Order $order): ?Operation
    {
        return $this->pickings->generateFor($order);
    }

    public function requestShipLater(Order $order): void
    {
        $this->shipLater->request($order);
    }

    public function generateSessionPicking(Session $session): ?Operation
    {
        return $this->sessionPickings->generateFor($session);
    }

    public function retryOrderPicking(Order $order): ?Operation
    {
        return $this->pickings->retry($order);
    }

    public function refreshSessionTotals(Session $session): Session
    {
        return $this->orders->refreshSessionTotals($session);
    }

    public function saveDraftOrder(array $payload): Order
    {
        return $this->processor->saveDraft($payload);
    }

    public function createFloor(Config $config, string $name): Floor
    {
        return app(FloorPlanEditor::class)->createFloor($config, $name);
    }

    public function saveFloor(Floor $floor, array $data): Floor
    {
        return app(FloorPlanEditor::class)->saveFloor($floor, $data);
    }

    public function setFloorBackgroundImage(Floor $floor, UploadedFile $image): Floor
    {
        return app(FloorPlanEditor::class)->setBackgroundImage($floor, $image);
    }

    public function removeFloorBackgroundImage(Floor $floor): Floor
    {
        return app(FloorPlanEditor::class)->removeBackgroundImage($floor);
    }

    public function deleteFloor(Floor $floor): void
    {
        app(FloorPlanEditor::class)->deleteFloor($floor);
    }

    public function saveDraftOrders(array $orders): array
    {
        return $this->processor->saveDraftBatch($orders);
    }

    public function discardDraftOrder(Order $order): void
    {
        $this->processor->discardDraft($order);
    }

    public function refundOrder(Order $order, array $lineQuantities, ?Session $session = null): Order
    {
        return $this->refunds->refund($order, $lineQuantities, $session);
    }

    public function settleRefund(Order $refund, ?int $paymentMethodId = null): Order
    {
        return $this->refunds->settle($refund, $paymentMethodId);
    }

    public function refundableLines(Order $order): array
    {
        return $this->refunds->refundableLines($order);
    }

    public function resolvePrice(Product $product, ?PriceList $priceList = null, float $quantity = 1.0): float
    {
        return $this->prices->resolve($product, $priceList, $quantity);
    }

    public function openSession(Config $config, ?int $userId = null): Session
    {
        return $this->sessions->open($config, $userId);
    }

    public function confirmSessionOpeningControl(Session $session, float $cashBalanceStart = 0.0, ?string $notes = null): Session
    {
        return $this->sessions->confirmOpeningControl($session, $cashBalanceStart, $notes);
    }

    public function requestSessionClosing(Session $session): Session
    {
        return $this->sessions->requestClosing($session);
    }

    public function closeSession(Session $session, ?float $cashBalanceEndReal = null, ?string $notes = null): Session
    {
        return $this->sessions->close($session, $cashBalanceEndReal, $notes);
    }

    public function closeSessionWithAccounting(Session $session, ?float $cashBalanceEndReal = null, ?string $notes = null, ?int $balancingAccountId = null, array $paymentDifferences = []): Session
    {
        return $this->sessionCloser->close($session, $cashBalanceEndReal, $notes, $balancingAccountId, $paymentDifferences);
    }

    public function assertSessionCanOpen(Config $config): void
    {
        $this->preflight->assertCanOpen($config);
    }

    public function invoiceOrder(Order $order): Move
    {
        return $this->invoicer->invoice($order);
    }

    public function openRescueSession(Session $session): Session
    {
        return $this->sessions->openRescueFor($session);
    }

    public function loginSession(Session $session): Session
    {
        return $this->sessions->login($session);
    }

    public function cashIn(Session $session, float $amount, ?string $reason = null): CashMovement
    {
        return $this->sessions->cashIn($session, $amount, $reason);
    }

    public function cashOut(Session $session, float $amount, ?string $reason = null): CashMovement
    {
        return $this->sessions->cashOut($session, $amount, $reason);
    }

    public function liveSessionFor(Config $config): ?Session
    {
        return $this->sessions->liveSessionFor($config);
    }

    public function isSessionDiscardable(Session $session): bool
    {
        return $this->sessions->isDiscardable($session);
    }

    public function discardSession(Session $session): void
    {
        $this->sessions->discard($session);
    }

    public function assertSessionOpen(Session $session): void
    {
        $this->sessions->assertOpen($session);
    }

    public function provisionWarehouse(InventoryWarehouse $warehouse): ?Warehouse
    {
        return $this->warehouses->provision($warehouse);
    }

    public function syncWarehouse(InventoryWarehouse $warehouse): ?Warehouse
    {
        return $this->warehouses->sync($warehouse);
    }

    public function provisionWarehouses(): void
    {
        $this->warehouses->provisionAll();
    }
}
