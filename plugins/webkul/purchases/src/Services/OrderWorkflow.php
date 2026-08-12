<?php

namespace Webkul\Purchase\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Webkul\Account\Models\Partner;
use Webkul\Purchase\Enums as PurchaseEnums;
use Webkul\Purchase\Events\OrderCanceled;
use Webkul\Purchase\Events\OrderConfirmed;
use Webkul\Purchase\Events\OrderDrafted;
use Webkul\Purchase\Events\OrderLocked;
use Webkul\Purchase\Events\OrderUnlocked;
use Webkul\Purchase\Mail\VendorPurchaseOrderMail;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Security\Enums\PermissionType;

class OrderWorkflow
{
    public function __construct(
        protected OrderCalculator $calculator,
        protected ReceiptPlanner $receipts,
        protected DocumentGenerator $documents,
    ) {}

    public function sendRequestForQuotation(Order $record, array $data): Order
    {
        $pdfPath = $this->documents->requestForQuotationPdf($record);

        $this->mailVendors($data, $pdfPath);

        $record->update(['state' => PurchaseEnums\OrderState::SENT]);

        $record = $this->calculator->recompute($record);

        $this->attachToChatter($record, $data, $pdfPath);

        return $record;
    }

    public function sendOrder(Order $record, array $data): Order
    {
        $pdfPath = $this->documents->purchaseOrderPdf($record);

        $this->mailVendors($data, $pdfPath);

        $this->attachToChatter($record, $data, $pdfPath);

        return $record;
    }

    protected function mailVendors(array $data, string $pdfPath): void
    {
        foreach ($data['vendors'] as $vendorId) {
            $vendor = Partner::find($vendorId);

            if ($vendor?->email) {
                Mail::to($vendor->email)->send(new VendorPurchaseOrderMail($data['subject'], $data['message'], $pdfPath));
            }
        }
    }

    protected function attachToChatter(Order $record, array $data, string $pdfPath): void
    {
        $message = $record->addMessage([
            'body' => Str::markdown($data['message']),
            'type' => 'comment',
        ]);

        $record->addAttachments([$pdfPath], ['message_id' => $message->id]);
    }

    public function confirm(Order $record): Order
    {
        $settings = settings(OrderSettings::class);

        $needsApproval = $settings->enable_order_approval
            && $record->total_amount >= $settings->order_validation_amount;

        if (! $needsApproval || $this->canApprove(Auth::user())) {
            return $this->approve($record, $settings);
        }

        $record->update(['state' => PurchaseEnums\OrderState::TO_APPROVE]);

        return $this->calculator->recompute($record);
    }

    protected function approve(Order $record, OrderSettings $settings): Order
    {
        $record->update([
            'state' => $settings->enable_lock_confirmed_orders
                ? PurchaseEnums\OrderState::DONE
                : PurchaseEnums\OrderState::PURCHASE,
            'approved_at' => now(),
        ]);

        $record = $this->calculator->recompute($record);

        $this->receipts->planForOrder($record);

        $this->calculator->refreshReceiptStatus($record->refresh())->save();

        $record = $record->refresh();

        OrderConfirmed::dispatch($record);

        return $record;
    }

    public function canApprove($user): bool
    {
        return (bool) $user
            && in_array($user->resource_permission, [PermissionType::GLOBAL, PermissionType::GROUP]);
    }

    public function cancel(Order $record): Order
    {
        $record->update([
            'state'                   => PurchaseEnums\OrderState::CANCELED,
            'mail_reminder_confirmed' => false,
        ]);

        $record = $this->calculator->recompute($record);

        $this->receipts->cancelOperations($record);

        OrderCanceled::dispatch($record);

        return $record;
    }

    public function backToDraft(Order $record): Order
    {
        return $this->transitionTo($record, PurchaseEnums\OrderState::DRAFT, OrderDrafted::class);
    }

    public function lock(Order $record): Order
    {
        return $this->transitionTo($record, PurchaseEnums\OrderState::DONE, OrderLocked::class);
    }

    public function unlock(Order $record): Order
    {
        return $this->transitionTo($record, PurchaseEnums\OrderState::PURCHASE, OrderUnlocked::class);
    }

    protected function transitionTo(Order $record, $state, string $event): Order
    {
        $record->update(['state' => $state]);

        $record = $this->calculator->recompute($record);

        $event::dispatch($record);

        return $record;
    }
}
