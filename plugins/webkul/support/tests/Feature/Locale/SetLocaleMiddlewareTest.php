<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    config()->set('app.supported_locales', [
        'en' => ['label' => 'English', 'native' => 'English', 'flag' => 'en', 'rtl' => false],
        'ar' => ['label' => 'Arabic',  'native' => 'العربية',  'flag' => 'ar', 'rtl' => true],
    ]);

    config()->set('app.locale', 'en');
    config()->set('app.fallback_locale', 'en');

    Session::flush();
    Auth::logout();
    App::setLocale('en');
});

function runSetLocale(array $query = [], array $sessionData = []): string
{
    foreach ($sessionData as $k => $v) {
        Session::put($k, $v);
    }

    $request = Request::create('/admin', 'GET', $query);
    $request->setLaravelSession(app('session.store'));
    $request->setUserResolver(fn () => Auth::user());

    (new SetLocale)->handle($request, fn () => response('ok'));

    return App::getLocale();
}

it('falls back to config app.locale when no preference exists', function () {
    config()->set('app.locale', 'en');

    expect(runSetLocale())->toBe('en');
});

it('uses APP_LOCALE env value via config when set', function () {
    config()->set('app.locale', 'ar');

    expect(runSetLocale())->toBe('ar');
});

it('falls back to fallback_locale when primary is unsupported', function () {
    config()->set('app.locale', 'fr');
    config()->set('app.fallback_locale', 'ar');

    expect(runSetLocale())->toBe('ar');
});

it('falls back to first supported locale when both primary and fallback are unsupported', function () {
    config()->set('app.locale', 'fr');
    config()->set('app.fallback_locale', 'de');

    expect(runSetLocale())->toBe('en');
});

it('honors ?lang= query parameter for guests', function () {
    expect(runSetLocale(['lang' => 'ar']))->toBe('ar');
});

it('ignores unsupported ?lang= query parameter', function () {
    expect(runSetLocale(['lang' => 'fr']))->toBe('en');
});

it('stores guest locale in session when ?lang= is used', function () {
    runSetLocale(['lang' => 'ar']);

    expect(Session::get('locale'))->toBe('ar');
});

it('reads guest locale from session on subsequent requests', function () {
    expect(runSetLocale([], ['locale' => 'ar']))->toBe('ar');
});

it('ignores unsupported locale stored in session', function () {
    expect(runSetLocale([], ['locale' => 'fr']))->toBe('en');
});

it('uses authenticated user language preference over session', function () {
    $user = User::withoutEvents(fn () => User::factory()->create(['language' => 'ar']));
    Auth::login($user);

    expect(runSetLocale([], ['locale' => 'en']))->toBe('ar');
});

it('clears stale session locale on authenticated requests', function () {
    $user = User::withoutEvents(fn () => User::factory()->create(['language' => null]));
    Auth::login($user);

    runSetLocale([], ['locale' => 'ar']);

    expect(Session::has('locale'))->toBeFalse();
});

it('does not persist ?lang= into the user language column', function () {
    $user = User::withoutEvents(fn () => User::factory()->create(['language' => null]));
    Auth::login($user);

    runSetLocale(['lang' => 'ar']);

    expect($user->fresh()->language)->toBeNull();
});

it('gives authenticated users config fallback when their language is null', function () {
    config()->set('app.locale', 'ar');
    $user = User::withoutEvents(fn () => User::factory()->create(['language' => null]));
    Auth::login($user);

    expect(runSetLocale())->toBe('ar');
});

it('lets ?lang= override user preference for the current request only', function () {
    $user = User::withoutEvents(fn () => User::factory()->create(['language' => 'ar']));
    Auth::login($user);

    expect(runSetLocale(['lang' => 'en']))->toBe('en')
        ->and($user->fresh()->language)->toBe('ar');
});
