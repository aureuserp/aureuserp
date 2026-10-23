<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../Helpers/CrmTestHelper.php';

/*
 * Note: App::setLocale() also writes config('app.locale'), which is the middleware's fallback,
 * so tests that change the current locale set the configured locale back explicitly.
 */
beforeEach(function () {
    CrmTestHelper::bootstrap();

    config(['app.locale' => 'en']);

    App::setLocale('en');

    Session::forget('locale');
});

/**
 * Runs the SetLocale middleware for a request to the admin panel and returns the resulting locale.
 *
 * @param  array<string, string>  $query
 */
function runCrmSetLocale(?User $user, array $query = []): string
{
    $request = Request::create('/admin', 'GET', $query);

    $request->setUserResolver(fn (): ?User => $user);

    $response = (new SetLocale)->handle($request, fn (Request $request): Response => new Response('ok'));

    expect($response->getContent())->toBe('ok');

    return App::getLocale();
}

it('uses the signed-in user\'s saved language over a stale session language', function () {
    $user = CrmTestHelper::actingAs();
    $user->forceFill(['language' => 'ar'])->saveQuietly();

    Session::put('locale', 'es');

    expect(runCrmSetLocale($user))->toBe('ar');
});

it('switches the language with the lang query parameter and remembers it', function () {
    $user = CrmTestHelper::actingAs();
    $user->forceFill(['language' => 'ar'])->saveQuietly();

    Session::put('locale', 'ar');

    expect(runCrmSetLocale($user, ['lang' => 'fr']))->toBe('fr')
        ->and(Session::get('locale'))->toBe('fr')
        ->and($user->fresh()->language)->toBe('fr');
});

it('keeps the switched language on the next request', function () {
    $user = CrmTestHelper::actingAs();
    $user->forceFill(['language' => 'en'])->saveQuietly();

    runCrmSetLocale($user, ['lang' => 'pt_BR']);

    App::setLocale('en');

    expect(runCrmSetLocale($user->fresh()))->toBe('pt_BR');
});

it('ignores an unsupported lang query parameter', function (string $unsupported) {
    $user = CrmTestHelper::actingAs();
    $user->forceFill(['language' => 'es'])->saveQuietly();

    Session::put('locale', 'es');

    expect(runCrmSetLocale($user, ['lang' => $unsupported]))->toBe('es')
        ->and(Session::get('locale'))->toBe('es')
        ->and($user->fresh()->language)->toBe('es');
})->with([
    'unknown code'    => 'xx',
    'wrong case'      => 'FR',
    'hyphenated code' => 'pt-BR',
]);

it('falls back to the session language when the user\'s saved language is unsupported', function () {
    $user = CrmTestHelper::actingAs();
    $user->forceFill(['language' => 'xx'])->saveQuietly();

    Session::put('locale', 'fr');

    expect(runCrmSetLocale($user))->toBe('fr');
});

it('uses the session language for guests', function () {
    Session::put('locale', 'pt_BR');

    expect(runCrmSetLocale(null))->toBe('pt_BR');
});

it('falls back to the application locale for guests without a session language', function () {
    App::setLocale('ar');

    config(['app.locale' => 'en']);

    expect(runCrmSetLocale(null))->toBe('en');
});

it('falls back to the application locale for guests with an unsupported session language', function () {
    Session::put('locale', 'zz');

    App::setLocale('ar');

    config(['app.locale' => 'en']);

    expect(runCrmSetLocale(null))->toBe('en');
});

it('switches a guest\'s language with the lang query parameter and stores it in the session', function () {
    expect(runCrmSetLocale(null, ['lang' => 'es']))->toBe('es')
        ->and(Session::get('locale'))->toBe('es');
});
