<?php

namespace Webkul\Crm\Filament\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Services\SMS\SMSSender;

class SendLeadSMSAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'sendSMS';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.send-sms.label'))
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('gray')
            ->modalHeading(__('crm::filament/lead-actions.send-sms.label'))
            ->modalSubmitActionLabel(__('crm::filament/lead-actions.send-sms.submit'))
            ->visible(fn (Model $record): bool => LeadResourceResolver::canEdit(LeadResourceResolver::leadOf($record)))
            ->fillForm(fn (Model $record): array => ['recipient' => SMSSender::recipientFor(LeadResourceResolver::leadOf($record))])
            ->schema(fn (Model $record): array => [
                TextInput::make('recipient')
                    ->label(__('crm::filament/lead-actions.send-sms.form.recipient'))
                    ->helperText(__('crm::filament/sms.form.international-helper'))
                    ->tel()
                    ->required()
                    ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                        if (! SMSSender::normalize($value, SMSSender::phoneCodeFor(LeadResourceResolver::leadOf($record)))) {
                            $fail(__('crm::filament/sms.errors.invalid-number'));
                        }
                    }),
                SendLeadsSMSBulkAction::messageField(),
            ])
            ->action(function (Model $record, array $data): void {
                if (SendLeadsSMSBulkAction::notifyWhenGatewayMissing()) {
                    return;
                }

                $lead = LeadResourceResolver::leadOf($record);
                $number = SMSSender::normalize($data['recipient'], SMSSender::phoneCodeFor($lead));

                if (! SendLeadsSMSBulkAction::sendTo($lead, $number, $data['message'])) {
                    Notification::make()
                        ->danger()
                        ->title(__('crm::filament/lead-actions.send-sms.failed'))
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.send-sms.sent', ['number' => '+'.$number]))
                    ->send();
            });
    }
}
