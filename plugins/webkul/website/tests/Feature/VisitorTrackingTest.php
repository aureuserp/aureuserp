<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Webkul\Website\Models\Page;
use Webkul\Website\Models\Partner;
use Webkul\Website\Models\Track;
use Webkul\Website\Models\Visitor;
use Webkul\Website\Services\IpGeolocation;
use Webkul\Website\Services\VisitorTracker;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(fn () => TestBootstrapHelper::ensurePluginInstalled('website'));

function visitorRequest(string $path = 'pages/about-us', ?string $cookie = null, string $userAgent = 'Mozilla/5.0 Chrome/120', array $headers = []): Request
{
    $server = ['HTTP_USER_AGENT' => $userAgent];

    foreach ($headers as $name => $value) {
        $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
    }

    return Request::create('http://site.test/'.$path, 'GET', [], $cookie ? [VisitorTracker::COOKIE => $cookie] : [], [], $server);
}

function visitorPage(bool $tracked = true): Page
{
    return Page::factory()->create(['is_published' => true, 'is_tracked' => $tracked]);
}

it('creates an anonymous visitor with a page view', function () {
    $page = visitorPage();

    $visitor = app(VisitorTracker::class)->track(visitorRequest(headers: ['CF-IPCountry' => 'IN']), null, $page);

    expect($visitor->access_token)->toMatch('/^[a-z0-9]{32}$/')
        ->and($visitor->partner_id)->toBeNull()
        ->and($visitor->visit_count)->toBe(1)
        ->and($visitor->country?->code)->toBe('IN')
        ->and($visitor->lang)->toBe(app()->getLocale())
        ->and($visitor->tracks()->pluck('page_id')->all())->toBe([$page->id]);
});

it('reuses the visitor from its cookie and does not repeat a page view within 30 minutes', function () {
    $page = visitorPage();
    $tracker = app(VisitorTracker::class);

    $visitor = $tracker->track(visitorRequest(), null, $page);
    $again = $tracker->track(visitorRequest(cookie: $visitor->access_token), null, $page);

    expect($again->id)->toBe($visitor->id)
        ->and($visitor->tracks()->count())->toBe(1);

    Track::query()->where('visitor_id', $visitor->id)->update(['visit_datetime' => now()->subHour()]);

    $tracker->track(visitorRequest(cookie: $visitor->access_token), null, $page);

    expect($visitor->tracks()->count())->toBe(2);
});

it('counts a new visit after 8 hours without activity', function () {
    $page = visitorPage();
    $tracker = app(VisitorTracker::class);

    $visitor = $tracker->track(visitorRequest(), null, $page);
    $visitor->forceFill(['last_connection_datetime' => now()->subHours(9)])->save();

    $tracker->track(visitorRequest(cookie: $visitor->access_token), null, $page);

    expect($visitor->fresh()->visit_count)->toBe(2);
});

it('ignores bots and does not add views of untracked pages', function () {
    $tracker = app(VisitorTracker::class);

    expect($tracker->track(visitorRequest(userAgent: 'Googlebot/2.1'), null, visitorPage()))->toBeNull();

    $visitor = $tracker->track(visitorRequest(), null, visitorPage(tracked: false));

    expect($visitor)->not->toBeNull()
        ->and($visitor->tracks()->count())->toBe(0);
});

it('stores the browser timezone and maps old names', function () {
    $request = visitorRequest();
    $request->cookies->set(VisitorTracker::TIMEZONE_COOKIE, 'Asia/Calcutta');

    $visitor = app(VisitorTracker::class)->track($request, null, visitorPage());

    expect($visitor->timezone)->toBe('Asia/Kolkata');
});

it('links the visitor to the contact on login and merges earlier visits', function () {
    $tracker = app(VisitorTracker::class);
    $partner = Partner::factory()->create();

    $first = $tracker->track(visitorRequest(), null, visitorPage());
    $linked = $tracker->linkPartner($partner, $first->access_token);

    expect($linked->id)->toBe($first->id)
        ->and($linked->partner_id)->toBe($partner->id)
        ->and($linked->access_token)->toBe('partner-'.$partner->id);

    $second = $tracker->track(visitorRequest(), null, visitorPage());
    $merged = $tracker->linkPartner($partner, $second->access_token);

    expect($merged->id)->toBe($first->id)
        ->and(Visitor::query()->whereKey($second->id)->exists())->toBeFalse()
        ->and($merged->tracks()->count())->toBe(2);
});

it('deletes anonymous visitors inactive for 60 days but keeps contacts', function () {
    $old = Visitor::query()->create(['access_token' => str_repeat('b', 32), 'last_connection_datetime' => now()->subDays(61)]);
    $recent = Visitor::query()->create(['access_token' => str_repeat('c', 32), 'last_connection_datetime' => now()]);
    $contact = Visitor::query()->create(['access_token' => 'partner-999999', 'partner_id' => Partner::factory()->create()->id, 'last_connection_datetime' => now()->subDays(90)]);

    Artisan::call('website:delete-inactive-visitors');

    expect(Visitor::query()->whereKey($old->id)->exists())->toBeFalse()
        ->and(Visitor::query()->whereKey($recent->id)->exists())->toBeTrue()
        ->and(Visitor::query()->whereKey($contact->id)->exists())->toBeTrue();
});

it('finds the country from the IP address and uses its timezone', function () {
    DB::table(IpGeolocation::TABLE)->insertOrIgnore([
        'ip_from'      => IpGeolocation::toBinary('49.36.0.0'),
        'ip_to'        => IpGeolocation::toBinary('49.36.255.255'),
        'country_code' => 'IN',
    ]);
    Cache::forever(IpGeolocation::CACHE_VERSION_KEY, (int) Cache::get(IpGeolocation::CACHE_VERSION_KEY, 0) + 1);

    $geolocation = app(IpGeolocation::class);

    expect($geolocation->countryCode('49.36.128.1'))->toBe('IN')
        ->and($geolocation->countryCode('192.168.1.10'))->toBeNull()
        ->and($geolocation->timezoneForCountry('IN'))->toBe('Asia/Kolkata')
        ->and($geolocation->timezoneForCountry('US'))->toBe('America/New_York');

    $request = Request::create('http://site.test/', 'GET', [], [], [], ['HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120', 'REMOTE_ADDR' => '49.36.128.1']);

    $visitor = app(VisitorTracker::class)->track($request, null, visitorPage());

    expect($visitor->country?->code)->toBe('IN')
        ->and($visitor->timezone)->toBe('Asia/Kolkata');
});

it('takes the country from the browser timezone when the IP address has none', function () {
    $request = Request::create('http://site.test/', 'GET', [], [VisitorTracker::TIMEZONE_COOKIE => 'Asia/Calcutta'], [], [
        'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120',
        'REMOTE_ADDR'     => '192.168.1.20',
    ]);

    $visitor = app(VisitorTracker::class)->track($request, null, null);

    expect($visitor->timezone)->toBe('Asia/Kolkata')
        ->and($visitor->country?->code)->toBe('IN')
        ->and(VisitorTracker::countryCodeForTimezone('UTC'))->toBeNull();
});
