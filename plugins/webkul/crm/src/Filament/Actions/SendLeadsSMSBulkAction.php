<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Throwable;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\SMS\SMSSender;

class SendLeadsSMSBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'sendLeadsSMS';
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
                static::messageField(),
            ])
            ->action(function (Collection $records, array $data): void {
                if (static::notifyWhenGatewayMissing()) {
                    return;
                }

                $sent = 0;

                foreach ($records as $lead) {
                    $number = SMSSender::normalize(SMSSender::recipientFor($lead), SMSSender::phoneCodeFor($lead));

                    if (! $number
                        || ! LeadResourceResolver::canEdit($lead)) {
                        continue;
                    }

                    if (static::sendTo($lead, $number, $data['message'])) {
                        $sent++;
                    }
                }

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.send-sms.notification', ['sent' => $sent, 'skipped' => $records->count() - $sent]))
                    ->send();
            });
    }

    public static function notifyWhenGatewayMissing(): bool
    {
        if (app(SMSSender::class)->isConfigured()) {
            return false;
        }

        Notification::make()
            ->danger()
            ->title(__('crm::filament/lead-actions.send-sms.failed'))
            ->body(__('crm::filament/sms.errors.not-configured'))
            ->send();

        return true;
    }

    public static function messageField(): Textarea
    {
        return Textarea::make('message')
            ->label(__('crm::filament/lead-actions.send-sms.form.message'))
            ->required()
            ->rows(4);
    }

    public static function sendTo(Lead $lead, string $number, string $message): bool
    {
        try {
            app(SMSSender::class)->send($number, $message);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        $lead->addMessage([
            'type'    => 'comment',
            'subject' => __('crm::filament/lead-actions.send-sms.log-subject', ['number' => '+'.$number]),
            'body'    => nl2br(e($message)),
        ]);

        return true;
    }
}
