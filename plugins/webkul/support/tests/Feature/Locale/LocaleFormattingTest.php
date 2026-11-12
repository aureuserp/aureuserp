<?php

use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\App;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    config()->set('app.supported_locales', [
        'en' => ['label' => 'English', 'native' => 'English',  'flag' => 'en', 'rtl' => false],
        'fr' => ['label' => 'French',  'native' => 'Français', 'flag' => 'fr', 'rtl' => false],
        'ar' => ['label' => 'Arabic',  'native' => 'العربية',  'flag' => 'ar', 'rtl' => true],
    ]);

    config()->set('app.locale', 'en');
});

afterEach(fn () => App::setLocale('en'));

function plain_spaces(string $value): string
{
    return preg_replace('/[\x{00A0}\x{202F}]/u', ' ', $value);
}

it('formats numbers in the current locale', function () {
    App::setLocale('fr');

    expect(plain_spaces(format_number(1234.567)))->toBe('1 234,57')
        ->and(plain_spaces(format_number(1234.567, 0)))->toBe('1 235');

    App::setLocale('en');

    expect(format_number(1234.567))->toBe('1,234.57');
});

it('keeps latin digits for right-to-left locales', function () {
    App::setLocale('ar');

    expect(format_number(1234.5))->toMatch('/^[0-9.,\s]+$/u');
});

it('formats money in the current locale instead of the application locale', function () {
    App::setLocale('fr');

    expect(plain_spaces(money(1234.5, 'EUR')))->toBe('1 234,50 €');

    App::setLocale('en');

    expect(money(1234.5, 'EUR'))->toBe('€1,234.50');
});

it('formats dates in the current locale', function () {
    App::setLocale('fr');

    expect(format_date('2026-10-28'))->toBe('28 oct. 2026')
        ->and(format_date(null))->toBe('');

    App::setLocale('en');

    expect(format_date('2026-10-28'))->toBe('Oct 28, 2026');
});

it('adds a translated suffix only to trashed records', function () {
    $user = User::factory()->create();

    expect(trashed_label_suffix($user))->toBe('')
        ->and(trashed_label_suffix(null))->toBe('');

    $user->delete();

    App::setLocale('fr');

    expect(trashed_label_suffix($user))->toBe(' '.__('support::support.trashed-suffix'))
        ->and(__('support::support.trashed-suffix'))->not->toBe('(Deleted)');
});

it('does not treat options of a select without a relationship as trashed', function () {
    $select = Select::make('status')->options([1 => 'Active']);

    expect(is_trashed_option($select, 1))->toBeFalse()
        ->and(is_trashed_option(null, 1))->toBeFalse();
});
