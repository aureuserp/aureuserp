<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Chatter\Models\Message;
use Webkul\Crm\Enums\ActivityDueStatus;

class Activity extends Message
{
    protected static function booted(): void
    {
        static::addGlobalScope('crm_activities', function (Builder $query): void {
            $query
                ->where('chatter_messages.type', 'activity')
                ->where('chatter_messages.messageable_type', (new Lead)->getMorphClass());
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'messageable_id');
    }

    public function getDueStatus(): ActivityDueStatus
    {
        return ActivityDueStatus::fromDate($this->date_deadline);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereDate('date_deadline', '<', today());
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('date_deadline', today());
    }

    public function scopePlanned(Builder $query): Builder
    {
        return $query->where(fn (Builder $query): Builder => $query
            ->whereNull('date_deadline')
            ->orWhereDate('date_deadline', '>', today()));
    }

    public function scopeDueStatus(Builder $query, ActivityDueStatus|string $status): Builder
    {
        return match (ActivityDueStatus::from($status instanceof ActivityDueStatus ? $status->value : $status)) {
            ActivityDueStatus::Overdue => $query->overdue(),
            ActivityDueStatus::Today   => $query->dueToday(),
            ActivityDueStatus::Planned => $query->planned(),
        };
    }

    public function markAsDone(?string $feedback = null): void
    {
        $this->lead?->addMessage([
            'type' => 'comment',
            'body' => collect([
                $this->activityType?->name ? $this->activityType->name.' done' : null,
                $this->summary ?: null,
                filled(trim(strip_tags((string) $this->body))) ? __('chatter::livewire/chatter-panel.process-message.original-note', ['body' => $this->body]) : null,
                $feedback ? __('chatter::livewire/chatter-panel.process-message.feedback', ['feedback' => $feedback]) : null,
            ])->filter()->implode(' '),
        ]);

        $this->delete();
    }
}
