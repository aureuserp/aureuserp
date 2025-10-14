<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Models\Order;

class SendByEmailAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'orders.sales.send-by-email';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color(fn (): string => $this->getRecord()->state === OrderState::DRAFT ? 'primary' : 'gray')
            ->beforeFormFilled(function (Order $record, Action $action) {
                $pdf = Pdf::loadView('sales::sales.quotation', compact('record'))
                    ->setPaper('A4', 'portrait')
                    ->setOption('defaultFont', 'Arial');

                $fileName = "$record->name-".time().'.pdf';
                $filePath = 'sales-orders/'.$fileName;

                Storage::disk('public')->put($filePath, $pdf->output());

                $action->fillForm([
                    'file'        => $filePath,
                    'partners'    => [$record->partner_id],
                    'subject'     => $record->partner->name.' Quotation (Ref '.$record->name.')',
                    'description' => 'Dear '.$record->partner->name.', <br/><br/>Your quotation <strong>'.$record->name.'</strong> amounting in <strong>'.$record->currency->symbol.' '.$record->amount_total.'</strong> is ready for review.<br/><br/>Should you have any questions or require further assistance, please feel free to reach out to us.',
                ]);
            })
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.title'))
            ->schema(
                function (Schema $schema) {
                    return $schema->components([
                        Select::make('partners')
                            ->options(Partner::all()->pluck('name', 'id'))
                            ->multiple()
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.form.fields.partners'))
                            ->searchable()
                            ->preload(),
                        TextInput::make('subject')
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.form.fields.subject'))
                            ->hiddenLabel(),
                        RichEditor::make('description')
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.form.fields.description'))
                            ->hiddenLabel(),
                        FileUpload::make('file')
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.form.fields.attachment'))
                            ->acceptedFileTypes([
                                'image/*',
                                'application/pdf',
                            ])
                            ->downloadable()
                            ->openable()
                            ->disk('public')
                            ->hiddenLabel(),
                    ]);
                }
            )
            ->modalIcon('heroicon-s-envelope')
            ->modalHeading(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.modal.heading'))
            ->hidden(fn (Order $record) => $record->state == OrderState::SALE)
            ->action(function (Order $record, array $data, Component $livewire) {
                $result = SaleOrder::sendQuotationOrOrderByEmail($record, $data);

                $this->handleEmailResults($result);

                $livewire->refreshFormData(['state']);
            });
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
                ->title(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.no_recipients.title'))
                ->body(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.no_recipients.body'))
                ->send();

            return;
        }

        if ($sentCount > 0 && $failedCount === 0) {
            Notification::make()
                ->success()
                ->title(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.all_success.title'))
                ->body($this->formatSuccessMessage($sent, $sentCount))
                ->send();

            return;
        }

        if ($sentCount === 0 && $failedCount > 0) {
            Notification::make()
                ->danger()
                ->title(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.all_failed.title'))
                ->body($this->formatFailureMessage($failed))
                ->send();

            return;
        }

        if ($sentCount > 0 && $failedCount > 0) {
            Notification::make()
                ->warning()
                ->title(__('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.partial_success.title'))
                ->body($this->formatMixedMessage($sent, $failed, $sentCount, $failedCount))
                ->send();
        }
    }

    private function formatSuccessMessage(array $sent, int $count): string
    {
        $recipients = implode(', ', $sent);

        return __('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.all_success.body', [
            'count'      => $count,
            'recipients' => $recipients,
            'plural'     => $count === 1
                ? __('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.quotation')
                : __('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.quotations'),
        ]);
    }

    private function formatFailureMessage(array $failed): string
    {
        $failedMessages = [];
        foreach ($failed as $partner => $reason) {
            $failedMessages[] = __('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.failure_item', [
                'partner' => $partner,
                'reason'  => $reason,
            ]);
        }

        return __('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.all_failed.body', [
            'failures' => implode('; ', $failedMessages),
        ]);
    }

    private function formatMixedMessage(array $sent, array $failed, int $sentCount, int $failedCount): string
    {
        $successPart = __('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.partial_success.sent_part', [
            'count'      => $sentCount,
            'recipients' => implode(', ', $sent),
        ]);

        $failedMessages = [];
        foreach ($failed as $partner => $reason) {
            $failedMessages[] = __('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.failure_item', [
                'partner' => $partner,
                'reason'  => $reason,
            ]);
        }

        $failurePart = __('sales::filament/clusters/orders/resources/quotation/actions/send-by-email.actions.notification.email.partial_success.failed_part', [
            'count'    => $failedCount,
            'failures' => implode('; ', $failedMessages),
        ]);

        return $successPart."\n\n".$failurePart;
    }
}
