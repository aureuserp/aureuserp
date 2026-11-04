<?php

namespace Webkul\Website\Listeners;

use Filament\Facades\Filament;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Webkul\Website\Enums\VisitorCookie;
use Webkul\Website\Filament\Customer\Pages\Homepage;
use Webkul\Website\Filament\Customer\Resources\PageResource;
use Webkul\Website\Models\Page;
use Webkul\Website\Services\VisitorTracker;

class TrackWebsiteVisit
{
    public function handle(RequestHandled $event): void
    {
        if (! $this->shouldTrack($event->request, $event->response)) {
            return;
        }

        try {
            $partner = Auth::guard('customer')->user();

            $visitor = app(VisitorTracker::class)->track($event->request, $partner, $this->page($event->request));

            if ($visitor 
                && ! $partner 
                && $event->request->cookie(VisitorCookie::VISITOR->value) !== $visitor->access_token) {
                $event->response->headers->setCookie(Cookie::create(VisitorCookie::VISITOR->value, $visitor->access_token, now()->addYear(), '/', null, $event->request->isSecure(), true, false, Cookie::SAMESITE_LAX));
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    protected function shouldTrack(Request $request, Response $response): bool
    {
        return str_starts_with((string) $request->route()?->getName(), 'filament.customer.')
            && $request->isMethod('GET')
            && ! $request->hasHeader('X-Livewire')
            && ! $request->ajax()
            && ! in_array(strtolower((string) ($request->header('Purpose') ?? $request->header('Sec-Purpose') ?? '')), ['prefetch', 'preview'], true)
            && $response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }

    protected function page(Request $request): ?Page
    {
        $panel = Filament::getPanel('customer');
        $routeName = (string) $request->route()?->getName();

        if ($routeName === PageResource::getRouteBaseName($panel).'.view') {
            $record = $request->route('record');

            return $record instanceof Page ? $record : Page::query()->where('slug', $record)->first();
        }

        if ($routeName === Homepage::getRouteName($panel)) {
            return Page::query()->where('slug', 'home')->first();
        }

        return null;
    }
}
