<?php

namespace Webkul\FullCalendar\Contracts;

interface HasEvents
{
    public function onEventClick(array $event): void;

    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool;

    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool;

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void;
}
