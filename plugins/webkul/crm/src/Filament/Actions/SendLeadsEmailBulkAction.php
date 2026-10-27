<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Mail\LeadMessageMail;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\Mail\OutgoingMailer;

class SendLeadsEmailBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'sendLeadsEmail';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.send-email.label'))
            ->icon('heroicon-o-envelope')
            ->modalHeading(__('crm::filament/lead-actions.send-email.label'))
            ->modalSubmitActionLabel(__('crm::filament/lead-actions.send-email.submit'))
            ->modalWidth('2xl')
            ->deselectRecordsAfterCompletion()
            ->schema(static::messageFields())
            ->action(function (Collection $records, array $data): void {
                $sent = 0;

                foreach ($records as $lead) {
                    if (blank($lead->email) || ! LeadResourceResolver::canEdit($lead)) {
                        continue;
                    }

                    if (static::sendTo($lead, $data['subject'], $data['body'])) {
                        $sent++;
                    }
                }

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.send-email.notification', ['sent' => $sent, 'skipped' => $records->count() - $sent]))
                    ->send();
            });
    }

    public static function messageFields(): array
    {
        return [
            TextInput::make('subject')
                ->label(__('crm::filament/lead-actions.send-email.form.subject'))
                ->required()
                ->maxLength(255),
            RichEditor::make('body')
                ->label(__('crm::filament/lead-actions.send-email.form.body'))
                ->required(),
        ];
    }

    public static function sendTo(Lead $lead, string $subject, string $body): bool
    {
        $subject = $subject.' [Lead #'.$lead->getKey().']';

        try {
            app(OutgoingMailer::class)->send(
                $lead->email,
                $lead->contact_name ?: $lead->name,
                new LeadMessageMail($subject, $body, Auth::user()?->name, $lead->team?->crmAlias?->email),
            );
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        $lead->addMessage([
            'type'    => 'comment',
            'subject' => $subject,
            'body'    => $body,
        ]);

        return true;
    }
}
