<?php

namespace Webkul\FullCalendar\Concerns;

use Carbon\Carbon;
use Webkul\FullCalendar\FullCalendarPlugin;

trait InteractsWithEvents
{
    public function onEventClick(array $event): void
    {
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }

        $this->mountAction('view', [
            'type'  => 'click',
            'event' => $event,
        ]);
    }

    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }

        $this->mountAction('edit', [
            'type'          => 'drop',
            'event'         => $event,
            'oldEvent'      => $oldEvent,
            'relatedEvents' => $relatedEvents,
            'delta'         => $delta,
            'oldResource'   => $oldResource,
            'newResource'   => $newResource,
        ]);

        return false;
    }

    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }

        $this->mountAction('edit', [
            'type'          => 'resize',
            'event'         => $event,
            'oldEvent'      => $oldEvent,
            'relatedEvents' => $relatedEvents,
            'startDelta'    => $startDelta,
            'endDelta'      => $endDelta,
        ]);

        return false;
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        [$start, $end] = $this->calculateTimezoneOffset($start, $end, $allDay);

        $this->mountAction('create', [
            'type'     => 'select',
            'start'    => $start,
            'end'      => $end,
            'allDay'   => $allDay,
            'resource' => $resource,
        ]);
    }

    public function refreshRecords(): void
    {
        $this->records = null;

        $this->dispatch('full-calendar--refresh');
    }

    protected function calculateTimezoneOffset(string $start, ?string $end, bool $allDay): array
    {
        $timezone = FullCalendarPlugin::make()->getTimezone();

        $start = Carbon::parse($start, $timezone);

        if ($end) {
            $end = Carbon::parse($end, $timezone);
        }

        if (
            ! is_null($end)
            && $allDay
        ) {
            $end->subDay()->endOfDay();
        }

        return [$start, $end, $allDay];
    }
}
