<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Webkul\Crm\Filament\Support\LeadResourceResolver;

class SendLeadEmailAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'sendEmail';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.send-email.label'))
            ->icon('heroicon-o-envelope')
            ->color('gray')
            ->modalHeading(__('crm::filament/lead-actions.send-email.label'))
            ->modalSubmitActionLabel(__('crm::filament/lead-actions.send-email.submit'))
            ->modalWidth('2xl')
            ->visible(fn (Model $record): bool => LeadResourceResolver::canEdit($lead = LeadResourceResolver::leadOf($record)) && filled($lead->email))
            ->fillForm(fn (Model $record): array => ['recipient' => LeadResourceResolver::leadOf($record)?->email])
            ->schema([
                TextInput::make('recipient')
                    ->label(__('crm::filament/lead-actions.send-email.form.recipient'))
                    ->disabled()
                    ->dehydrated(false),
                ...SendLeadsEmailBulkAction::messageFields(),
            ])
            ->action(function (Model $record, array $data): void {
                $lead = LeadResourceResolver::leadOf($record);

                if (! SendLeadsEmailBulkAction::sendTo($lead, $data['subject'], $data['body'])) {
                    Notification::make()
                        ->danger()
                        ->title(__('crm::filament/lead-actions.send-email.failed'))
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.send-email.sent', ['email' => $lead->email]))
                    ->send();
            });
    }
}
