<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Actions;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Webkul\Account\Models\Partner;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Facades\PurchaseOrder;
use Webkul\Purchase\Models\Order;

class SendPOEmailAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'purchases.orders.send-po-email';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(fn (Order $record) => __('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.label'))
            ->schema(fn (Order $record) => [
                Select::make('vendors')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.form.fields.to'))
                    ->options(fn () => Partner::get()->mapWithKeys(fn ($partner) => [
                        $partner->id => $partner->email
                            ? "{$partner->name} <{$partner->email}>"
                            : $partner->name,
                    ])->toArray())
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->default([$record->partner_id]),

                TextInput::make('subject')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.form.fields.subject'))
                    ->required()
                    ->default("Purchase Order #{$record->name}"),

                MarkdownEditor::make('message')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.form.fields.message'))
                    ->required()
                    ->default(function () use ($record) {
                        $userName = Auth::user()->name;

                        return <<<MD
Dear **{$record->partner->name}**

Here is in attachment a purchase order **{$record->name}** amounting to **{$record->total_amount}**.

The receipt is expected for **{$record->planned_at}**.

Could you please acknowledge the receipt of this order?

Best regards,

--
{$userName}
MD;
                    }),

                FileUpload::make('attachment')
                    ->hiddenLabel()
                    ->disk('public')
                    ->default(fn () => PurchaseOrder::generatePurchaseOrderPdf($record))
                    ->acceptedFileTypes([
                        'image/*',
                        'application/pdf',
                    ])
                    ->downloadable()
                    ->openable(),
            ])
            ->action(function (array $data, Order $record, Component $livewire) {
                try {
                    $result = PurchaseOrder::sendPurchaseOrder($record, $data);
                    $this->handleEmailResults($result);

                } catch (Exception $e) {
                    Notification::make()
                        ->body($e->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                $livewire->updateForm();

            })
            ->color(fn (Order $record): string => $record->state === OrderState::DRAFT ? 'primary' : 'gray'
            )
            ->visible(fn (Order $record) => $record->state == OrderState::PURCHASE
            );
    }

    private function handleEmailResults(array $result): void
    {
        $sent = $result['sent'] ?? [];
        $failed = $result['failed'] ?? [];

        $sentCount = count($sent);
        $failedCount = count($failed);
        $totalCount = $sentCount + $failedCount;

        if ($totalCount === 0) {
            Notification::make()
                ->warning()
                ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.no_recipients.title'))
                ->body(__('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.no_recipients.body'))
                ->send();

            return;
        }

        if ($sentCount > 0 && $failedCount === 0) {
            Notification::make()
                ->success()
                ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.all_success.title'))
                ->body($this->formatSuccessMessage($sent, $sentCount))
                ->send();

            return;
        }

        if ($sentCount === 0 && $failedCount > 0) {
            Notification::make()
                ->danger()
                ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.all_failed.title'))
                ->body($this->formatFailureMessage($failed))
                ->send();

            return;
        }

        if ($sentCount > 0 && $failedCount > 0) {
            Notification::make()
                ->warning()
                ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.partial_success.title'))
                ->body($this->formatMixedMessage($sent, $failed, $sentCount, $failedCount))
                ->send();
        }
    }

    private function formatSuccessMessage(array $sent): string
    {
        $recipients = implode(', ', $sent);

        return __('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.all_success.body', [
            'recipients' => $recipients,
        ]);
    }

    private function formatFailureMessage(array $failed): string
    {
        $failedMessages = [];

        foreach ($failed as $partner => $reason) {
            $failedMessages[] = __('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.failure_item', [
                'partner' => $partner,
                'reason'  => $reason,
            ]);
        }

        return __('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.all_failed.body', [
            'failures' => implode('; ', $failedMessages),
        ]);
    }

    private function formatMixedMessage(array $sent, array $failed, int $sentCount, int $failedCount): string
    {
        $successPart = __('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.partial_success.sent_part', [
            'count'      => $sentCount,
            'recipients' => implode(', ', $sent),
        ]);

        $failedMessages = [];
        
        foreach ($failed as $partner => $reason) {
            $failedMessages[] = __('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.failure_item', [
                'partner' => $partner,
                'reason'  => $reason,
            ]);
        }

        $failurePart = __('purchases::filament/admin/clusters/orders/resources/order/actions/send-po-email.actions.notification.email.partial_success.failed_part', [
            'count'    => $failedCount,
            'failures' => implode('; ', $failedMessages),
        ]);

        return $successPart."\n\n".$failurePart;
    }
}
