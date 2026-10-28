<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Throwable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Services\SMS\SMSSender;
use Webkul\Partner\Models\Partner;

class SendContactsSMSBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'sendContactsSMS';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.send-sms.label'))
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->modalHeading(__('crm::filament/lead-actions.send-sms.label'))
            ->modalSubmitActionLabel(__('crm::filament/lead-actions.send-sms.submit'))
            ->deselectRecordsAfterCompletion()
            ->schema([
                SendLeadsSMSBulkAction::messageField(),
            ])
            ->action(function (Collection $records, array $data): void {
                $sent = 0;

                foreach ($records as $contact) {
                    $number = SMSSender::normalize($contact->mobile ?: $contact->phone, $contact->country?->phone_code);

                    if (! $number
                        || $contact->trashed()
                        || ! ContactResource::canEdit($contact)) {
                        continue;
                    }

                    if (static::sendTo($contact, $number, $data['message'])) {
                        $sent++;
                    }
                }

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.send-sms.notification', ['sent' => $sent, 'skipped' => $records->count() - $sent]))
                    ->send();
            });
    }

    public static function sendTo(Partner $contact, string $number, string $message): bool
    {
        try {
            app(SMSSender::class)->send($number, $message);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        $contact->addMessage([
            'type'    => 'comment',
            'subject' => __('crm::filament/lead-actions.send-sms.log-subject', ['number' => '+'.$number]),
            'body'    => nl2br(e($message)),
        ]);

        return true;
    }
}
