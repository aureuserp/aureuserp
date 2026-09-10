<?php

namespace Webkul\Sale\Services;

use Exception;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Mail\SaleOrderCancelQuotation;
use Webkul\Sale\Mail\SaleOrderQuotation;
use Webkul\Sale\Models\Order;
use Webkul\Support\Services\EmailService;

class OrderMailer
{
    public function sendQuotation(Order $record, array $data): array
    {
        $sent = [];

        $failed = [];

        foreach (Partner::whereIn('id', $data['partners'])->get() as $partner) {
            if (empty($partner->email)) {
                $failed[$partner->name] = 'No email address';

                continue;
            }

            try {
                $this->deliverQuotation($record, $partner, $data);

                $sent[] = $partner->name;
            } catch (Exception $exception) {
                $failed[$partner->name] = 'Email service error: '.$exception->getMessage();
            }
        }

        if ($sent !== [] && $record->state === OrderState::DRAFT) {
            $record->state = OrderState::SENT;

            $record->save();
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    protected function deliverQuotation(Order $record, Partner $partner, array $data): void
    {
        $view = 'sales::mails.sale-order-quotation';

        $payload = $this->payload($record, $partner, $data, $record->state->getLabel());

        app(EmailService::class)->send(
            mailClass: SaleOrderQuotation::class,
            view: $view,
            payload: $payload,
            attachments: [[
                'path' => $data['file'],
                'name' => basename($data['file']),
            ]]
        );

        $message = $record->addMessage([
            'from' => ['company' => current_company()->toArray()],
            'body' => view($view, compact('payload'))->render(),
            'type' => 'comment',
        ]);

        $record->addAttachments([$data['file']], ['message_id' => $message->id]);
    }

    public function sendCancellation(Order $record, array $data): void
    {
        $view = 'sales::mails.sale-order-cancel-quotation';

        foreach (Partner::whereIn('id', $data['partners'])->get() as $partner) {
            $payload = $this->payload($record, $partner, $data, 'Quotation');

            app(EmailService::class)->send(
                mailClass: SaleOrderCancelQuotation::class,
                view: $view,
                payload: $payload,
            );

            $record->addMessage([
                'from' => ['company' => current_company()->toArray()],
                'body' => view($view, compact('payload'))->render(),
                'type' => 'comment',
            ]);
        }
    }

    protected function payload(Order $record, ?Partner $partner, array $data, string $modelName): array
    {
        return [
            'record_name' => $record->name,
            'model_name'  => $modelName,
            'subject'     => $data['subject'],
            'description' => $data['description'],
            'to'          => [
                'address' => $partner?->email,
                'name'    => $partner?->name,
            ],
        ];
    }
}
