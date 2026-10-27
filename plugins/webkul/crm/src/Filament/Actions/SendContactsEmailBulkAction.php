<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Mail\LeadMessageMail;
use Webkul\Crm\Services\Mail\OutgoingMailer;
use Webkul\Partner\Models\Partner;

class SendContactsEmailBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'sendContactsEmail';
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
            ->schema(SendLeadsEmailBulkAction::messageFields())
            ->action(function (Collection $records, array $data): void {
                $sent = $records
                    ->filter(fn (Partner $contact): bool => filled($contact->email) && ! $contact->trashed() && ContactResource::canEdit($contact))
                    ->filter(fn (Partner $contact): bool => static::sendTo($contact, $data['subject'], $data['body']))
                    ->count();

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.send-email.notification', ['sent' => $sent, 'skipped' => $records->count() - $sent]))
                    ->send();
            });
    }

    public static function sendTo(Partner $contact, string $subject, string $body): bool
    {
        try {
            app(OutgoingMailer::class)->send($contact->email, $contact->name, new LeadMessageMail($subject, $body, Auth::user()?->name));
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        $contact->addMessage([
            'type'    => 'comment',
            'subject' => $subject,
            'body'    => $body,
        ]);

        return true;
    }
}
