<?php

namespace Webkul\Crm\Listeners;

use Throwable;
use Webkul\Crm\Services\VisitsToLeads;
use Webkul\Website\Events\VisitorTracked;

class CreateLeadFromVisitor
{
    public function __construct(protected VisitsToLeads $visitsToLeads) {}

    public function handle(VisitorTracked $event): void
    {
        if (! VisitsToLeads::enabled()) {
            return;
        }

        if ($this->visitsToLeads->queue($event->visitor, $event->ip, $event->url)->isEmpty()) {
            return;
        }

        $visitorId = (int) $event->visitor->getKey();

        dispatch(function () use ($visitorId): void {
            try {
                app(VisitsToLeads::class)->processVisitor($visitorId);
            } catch (Throwable $exception) {
                report($exception);
            }
        })->afterResponse();
    }
}
