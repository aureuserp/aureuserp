<?php

namespace Webkul\Purchase;

use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Purchase\Services\Biller;
use Webkul\Purchase\Services\DocumentGenerator;
use Webkul\Purchase\Services\OrderCalculator;
use Webkul\Purchase\Services\OrderWorkflow;
use Webkul\Purchase\Services\ReceiptPlanner;
use Webkul\Purchase\Settings\OrderSettings;

class PurchaseOrder
{
    public function __construct(
        protected OrderWorkflow $workflow,
        protected OrderCalculator $calculator,
        protected ReceiptPlanner $receipts,
        protected Biller $biller,
        protected DocumentGenerator $documents,
    ) {}

    public static function getOrderSettings(): OrderSettings
    {
        return settings(OrderSettings::class);
    }

    public function sendRFQ(Order $record, array $data): Order
    {
        return $this->workflow->sendRequestForQuotation($record, $data);
    }

    public function sendPurchaseOrder(Order $record, array $data): Order
    {
        return $this->workflow->sendOrder($record, $data);
    }

    public function confirmPurchaseOrder(Order $record): Order
    {
        return $this->workflow->confirm($record);
    }

    public function canUserApprove($user): bool
    {
        return $this->workflow->canApprove($user);
    }

    public function cancelPurchaseOrder(Order $record): Order
    {
        return $this->workflow->cancel($record);
    }

    public function draftPurchaseOrder(Order $record): Order
    {
        return $this->workflow->backToDraft($record);
    }

    public function lockPurchaseOrder(Order $record): Order
    {
        return $this->workflow->lock($record);
    }

    public function unlockPurchaseOrder(Order $record): Order
    {
        return $this->workflow->unlock($record);
    }

    public function createPurchaseOrderBill(Order $record): Order
    {
        $this->biller->createBill($record);

        return $this->calculator->recompute($record);
    }

    public function computePurchaseOrder(Order $record): Order
    {
        return $this->calculator->recompute($record);
    }

    public function computePurchaseOrderLine(OrderLine $line): OrderLine
    {
        return $this->calculator->recomputeLine($line);
    }

    public function computeReceiptStatus(Order $order): Order
    {
        return $this->calculator->refreshReceiptStatus($order);
    }

    public function computeInvoiceStatus(Order $order): Order
    {
        return $this->calculator->refreshInvoiceStatus($order);
    }

    public function createOrUpdateInventoryOperation($lines): void
    {
        $this->receipts->syncFromLines($lines);
    }

    public function procuredQuantity(OrderLine $line): float
    {
        return $this->receipts->procuredQuantity($line);
    }

    public function outgoingAndIncomingMoves(OrderLine $line): array
    {
        return $this->receipts->outgoingAndIncomingMoves($line);
    }

    public function generateRFQPdf($record)
    {
        return $this->documents->requestForQuotationPdf($record);
    }

    public function generatePurchaseOrderPdf($record)
    {
        return $this->documents->purchaseOrderPdf($record);
    }

    public function createAccountMove($record): void
    {
        $this->biller->createBill($record);
    }
}
