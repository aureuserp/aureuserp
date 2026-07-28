<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\Profile;

require_once __DIR__.'/../../Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    config()->set('app.supported_locales', [
        'en' => ['label' => 'English', 'native' => 'English', 'flag' => 'en', 'rtl' => false],
        'ar' => ['label' => 'Arabic',  'native' => 'العربية',  'flag' => 'ar', 'rtl' => true],
    ]);

    Session::flush();
    Auth::logout();

    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function loginAsUser(array $attributes = []): User
{
    $user = User::factory()->create($attributes);

    test()->actingAs($user, 'web');

    return $user->fresh();
}

it('loads profile form pre-filled with current user language', function () {
    loginAsUser(['language' => 'ar']);

    Livewire::test(Profile::class)
        ->assertFormSet(['language' => 'ar'], 'editProfileForm');
});

it('persists language change to users table', function () {
    $user = loginAsUser(['language' => 'en']);

    Livewire::test(Profile::class)
        ->fillForm([
            'name'     => $user->name,
            'email'    => $user->email,
            'language' => 'ar',
        ], 'editProfileForm')
        ->call('updateProfile');

    expect($user->fresh()->language)->toBe('ar');
});

it('rejects unsupported language values when saving profile', function () {
    $user = loginAsUser(['language' => 'en']);

    Livewire::test(Profile::class)
        ->fillForm([
            'name'     => $user->name,
            'email'    => $user->email,
            'language' => 'fr',
        ], 'editProfileForm')
        ->call('updateProfile');

    expect($user->fresh()->language)->toBe('en');
});

it('keeps other profile fields unchanged when only language is updated', function () {
    $user = loginAsUser([
        'name'     => 'Jane Doe',
        'email'    => 'jane@example.com',
        'language' => 'en',
    ]);

    Livewire::test(Profile::class)
        ->fillForm([
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'language' => 'ar',
        ], 'editProfileForm')
        ->call('updateProfile');

    $fresh = $user->fresh();

    expect($fresh->name)->toBe('Jane Doe')
        ->and($fresh->email)->toBe('jane@example.com')
        ->and($fresh->language)->toBe('ar');
});
