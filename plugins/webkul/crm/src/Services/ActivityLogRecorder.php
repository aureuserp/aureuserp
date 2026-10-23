<?php

namespace Webkul\Crm\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Chatter\Models\Message;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;

class ActivityLogRecorder
{
    protected const MATCH_WINDOW_SECONDS = 10;

    protected array $postedMessages = [];

    protected bool $isRegistered = false;

    public function register(): void
    {
        if ($this->isRegistered) {
            return;
        }

        $this->isRegistered = true;

        Message::created(fn (Message $message) => $this->rememberPostedMessage($message));

        Message::deleted(fn (Message $message) => $this->recordIfDone($message));

        Activity::deleted(fn (Activity $activity) => $this->recordIfDone($activity));
    }

    protected function rememberPostedMessage(Message $message): void
    {
        if ($message->type !== 'comment' || ! $this->isOnLead($message)) {
            return;
        }

        $this->postedMessages[(int) $message->messageable_id][] = [
            'body' => (string) $message->body,
            'at'   => microtime(true),
        ];
    }

    protected function recordIfDone(Message $activity): void
    {
        if ($activity->type !== 'activity' || ! $this->isOnLead($activity)) {
            return;
        }

        $doneMessage = $this->pullDoneMessage($activity);

        if ($doneMessage === null) {
            return;
        }

        $lead = Lead::withTrashed()->find($activity->messageable_id);

        if (! $lead) {
            return;
        }

        ActivityLog::query()->create([
            'lead_id'             => $lead->getKey(),
            'lead_type'           => $lead->type,
            'activity_type_id'    => $activity->activity_type_id,
            'assigned_to'         => $activity->assigned_to,
            'done_by'             => Auth::id(),
            'user_id'             => $lead->user_id,
            'team_id'             => $lead->team_id,
            'stage_id'            => $lead->stage_id,
            'company_id'          => $lead->company_id,
            'summary'             => $activity->summary,
            'note'                => $doneMessage,
            'date_deadline'       => $activity->date_deadline,
            'activity_created_at' => $activity->created_at,
            'done_at'             => now(),
        ]);
    }

    protected function pullDoneMessage(Message $activity): ?string
    {
        $leadId = (int) $activity->messageable_id;

        $prefix = $activity->activityType?->name
            ? $activity->activityType->name.' done'
            : (string) $activity->summary;

        if ($prefix === '') {
            return null;
        }

        $threshold = microtime(true) - self::MATCH_WINDOW_SECONDS;

        foreach (array_reverse($this->postedMessages[$leadId] ?? [], preserve_keys: true) as $index => $posted) {
            if ($posted['at'] < $threshold || ! Str::startsWith($posted['body'], $prefix)) {
                continue;
            }

            unset($this->postedMessages[$leadId][$index]);

            return $posted['body'];
        }

        return null;
    }

    protected function isOnLead(Message $message): bool
    {
        return $message->messageable_type === (new Lead)->getMorphClass();
    }
}
