<?php

namespace Webkul\Chatter\Services;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Webkul\Chatter\Mail\MessageMail;
use Webkul\Chatter\Models\Message;
use Webkul\Partner\Models\Partner;

class ChatterNotificationService
{
    protected string $mailView = 'chatter::mail.message-mail';

    public function notifyFollowers(Message $message): void
    {
        $record = $message->messageable;

        if (! $record || ! method_exists($record, 'followers')) {
            return;
        }

        $followers = $record->followers()->with('partner')->get();

        if ($followers->isEmpty()) {
            return;
        }

        $causer = $message->causer;
        $authorPartnerId = $this->resolveAuthorPartnerId($causer);

        $from = $this->resolveFrom($message, $causer, $record);
        $recordName = $record->name ?? (string) $record->getKey();
        $recordUrl = $this->resolveRecordUrl($record);

        foreach ($followers as $follower) {
            $partner = $follower->partner;

            if (! $partner?->email) {
                continue;
            }

            if ($authorPartnerId && (int) $partner->id === (int) $authorPartnerId) {
                continue;
            }

            $payload = [
                'record_url'  => $recordUrl,
                'record_name' => $recordName,
                'model_name'  => class_basename($record),
                'subject'     => __('chatter::filament/resources/actions/chatter/message-action.setup.actions.mail.subject', [
                    'record_name' => $recordName,
                ]),
                'content'     => $this->buildContent($message),
                'from'        => $from,
                'to'          => [
                    'address' => $partner->email,
                    'name'    => $partner->name,
                ],
            ];

            try {
                Mail::to($partner->email, '"'.addslashes((string) $partner->name).'"')
                    ->send(new MessageMail($this->mailView, $payload));
            } catch (Throwable $e) {
                report($e);
            }
        }
    }

    protected function buildContent(Message $message): string
    {
        $content = $message->body ?? $message->summary ?? '';

        $changes = is_array($message->properties) ? $message->properties : [];

        if ($message->event === 'created' || empty($changes)) {
            return $content;
        }

        $rows = [];

        foreach ($changes as $field => $change) {
            if (! is_array($change)) {
                continue;
            }

            $label = ucwords(str_replace('_', ' ', (string) $field));
            $old = $change['old_value'] ?? null;
            $new = $change['new_value'] ?? null;
            $old = is_array($old) ? implode(', ', $old) : $old;
            $new = is_array($new) ? implode(', ', $new) : $new;

            if (isset($change['old_value']) && isset($change['new_value'])) {
                $rows[] = e($label).': '.e((string) $old).' → '.e((string) $new);
            } elseif (isset($change['new_value'])) {
                $rows[] = e($label).': '.e((string) $new);
            }
        }

        if (empty($rows)) {
            return $content;
        }

        return $content.'<ul><li>'.implode('</li><li>', $rows).'</li></ul>';
    }

    protected function resolveAuthorPartnerId(mixed $causer): ?int
    {
        if (! $causer) {
            return null;
        }

        if ($causer instanceof Partner) {
            return (int) $causer->id;
        }

        return $causer->partner_id ? (int) $causer->partner_id : null;
    }

    protected function resolveFrom(Message $message, mixed $causer, mixed $record): array
    {
        $from = [
            'address' => $causer?->email ?? config('mail.from.address'),
            'name'    => $causer?->name ?? config('mail.from.name'),
        ];

        $company = $message->company ?? ($record->company ?? null);

        if ($company) {
            $from['company'] = $company->toArray();
        }

        return $from;
    }

    protected function resolveRecordUrl(mixed $record): string
    {
        if (method_exists($record, 'getChatterResourceUrl')) {
            return (string) $record->getChatterResourceUrl();
        }

        try {
            $panel = Filament::getCurrentPanel();

            if (! $panel) {
                return '';
            }

            foreach ($panel->getResources() as $resource) {
                if (! ($record instanceof ($resource::getModel()))) {
                    continue;
                }

                $pages = $resource::getPages();

                foreach (['view', 'edit'] as $page) {
                    if (array_key_exists($page, $pages)) {
                        return $resource::getUrl($page, ['record' => $record->getKey()]);
                    }
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return '';
    }
}
