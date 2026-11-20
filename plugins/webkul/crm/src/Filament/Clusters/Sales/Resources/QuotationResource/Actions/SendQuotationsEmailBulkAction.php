<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Models\Order;

class SendQuotationsEmailBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'sendQuotationsEmail';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/quotation.actions.send-email.label'))
            ->icon('heroicon-o-envelope')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-envelope')
            ->modalDescription(__('crm::filament/clusters/sales/resources/quotation.actions.send-email.description'))
            ->modalSubmitActionLabel(__('crm::filament/clusters/sales/resources/quotation.actions.send-email.submit'))
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $sent = $records
                    ->filter(fn (Order $order): bool => static::canSend($order))
                    ->filter(fn (Order $order): bool => static::send($order))
                    ->count();

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/quotation.actions.send-email.notification', ['sent' => $sent, 'skipped' => $records->count() - $sent]))
                    ->send();
            });
    }

    public static function canSend(Order $order): bool
    {
        return ! $order->trashed()
            && in_array($order->state, [OrderState::DRAFT, OrderState::SENT], true)
            && filled($order->partner?->email)
            && QuotationResource::canEdit($order);
    }

    public static function send(Order $order): bool
    {
        try {
            $filePath = 'sales-orders/'.$order->name.'-'.time().'.pdf';

            Storage::disk('public')->put($filePath, Pdf::loadView('sales::sales.quotation', ['record' => $order, 'isPdf' => true])
                ->setPaper('A4', 'portrait')
                ->setOption('defaultFont', 'Arial')
                ->output());

            $result = SaleOrder::sendQuotationOrOrderByEmail($order, [
                'file'        => $filePath,
                'partners'    => [$order->partner_id],
                'subject'     => __('crm::filament/clusters/sales/resources/quotation.actions.send-email.subject', ['customer' => $order->partner->name, 'name' => $order->name]),
                'description' => __('crm::filament/clusters/sales/resources/quotation.actions.send-email.body', [
                    'customer' => e($order->partner->name),
                    'name'     => e($order->name),
                    'amount'   => e(money((float) $order->amount_total, $order->currency?->name)),
                ]),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        return filled($result['sent'] ?? []);
    }
}
