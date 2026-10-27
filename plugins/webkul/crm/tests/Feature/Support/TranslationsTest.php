<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function crmPath(string $path = ''): string
{
    return base_path('plugins/webkul/crm'.($path === '' ? '' : '/'.$path));
}

/**
 * Translation files of a language, keyed by their path relative to the language folder (without `.php`).
 *
 * @return array<string, string>
 */
function crmLangFiles(string $locale): array
{
    $directory = crmPath("resources/lang/{$locale}");

    return collect(File::allFiles($directory))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
        ->mapWithKeys(fn (SplFileInfo $file): array => [
            str_replace('\\', '/', substr($file->getPathname(), strlen($directory) + 1, -4)) => $file->getPathname(),
        ])
        ->sortKeys()
        ->all();
}

/**
 * @return array<string, mixed>
 */
function crmFlattenedTranslations(string $path): array
{
    $translations = require $path;

    return Arr::dot(is_array($translations) ? $translations : []);
}

/**
 * The unique `:placeholders` used in a translation line.
 *
 * @return array<int, string>
 */
function crmPlaceholders(mixed $line): array
{
    preg_match_all('/:([A-Za-z_]+)/', (string) $line, $matches);

    return collect($matches[1])->unique()->sort()->values()->all();
}

it('ships a translation file for every english file', function (string $locale) {
    expect(array_keys(crmLangFiles($locale)))->toBe(array_keys(crmLangFiles('en')));
})->with(['ar', 'es', 'fr', 'pt_BR']);

it('has exactly the english keys in every translation file', function (string $locale) {
    $translatedFiles = crmLangFiles($locale);

    foreach (crmLangFiles('en') as $relativePath => $englishPath) {
        expect($translatedFiles)->toHaveKey($relativePath);

        $englishKeys = array_keys(crmFlattenedTranslations($englishPath));
        $translatedKeys = array_keys(crmFlattenedTranslations($translatedFiles[$relativePath]));

        sort($englishKeys);
        sort($translatedKeys);

        expect($translatedKeys)->toBe($englishKeys, "{$locale}/{$relativePath}.php keys differ from English");
    }
})->with(['ar', 'es', 'fr', 'pt_BR']);

it('keeps every placeholder in every translation', function (string $locale) {
    $translatedFiles = crmLangFiles($locale);

    foreach (crmLangFiles('en') as $relativePath => $englishPath) {
        expect($translatedFiles)->toHaveKey($relativePath);

        $translated = crmFlattenedTranslations($translatedFiles[$relativePath]);

        foreach (crmFlattenedTranslations($englishPath) as $key => $line) {
            expect(crmPlaceholders($translated[$key] ?? ''))
                ->toBe(crmPlaceholders($line), "{$locale}/{$relativePath}.php [{$key}] placeholders differ from English");
        }
    }
})->with(['ar', 'es', 'fr', 'pt_BR']);

it('leaves no translation empty when the english line has text', function (string $locale) {
    $translatedFiles = crmLangFiles($locale);

    foreach (crmLangFiles('en') as $relativePath => $englishPath) {
        expect($translatedFiles)->toHaveKey($relativePath);

        $translated = crmFlattenedTranslations($translatedFiles[$relativePath]);

        foreach (crmFlattenedTranslations($englishPath) as $key => $line) {
            if (blank($line)) {
                continue;
            }

            expect(filled($translated[$key] ?? null))->toBeTrue("{$locale}/{$relativePath}.php [{$key}] is empty");
        }
    }
})->with(['ar', 'es', 'fr', 'pt_BR']);

it('covers every supported application language', function () {
    $supportedLocales = array_keys(config('app.supported_locales'));

    expect($supportedLocales)->not->toBeEmpty();

    foreach ($supportedLocales as $locale) {
        expect(File::isDirectory(crmPath("resources/lang/{$locale}")))->toBeTrue("Missing CRM translations for {$locale}")
            ->and(crmLangFiles($locale))->not->toBeEmpty();
    }
});

it('defines every static crm translation key referenced in code and views', function () {
    $files = collect([
        ...File::allFiles(crmPath('src')),
        ...File::allFiles(crmPath('resources/views')),
    ])->filter(fn (SplFileInfo $file): bool => str_ends_with($file->getFilename(), '.php'));

    $keys = [];

    foreach ($files as $file) {
        preg_match_all(
            '/(?:__|trans|trans_choice|@lang)\(\s*([\'"])(crm::[^\'"]+)\1/',
            File::get($file->getPathname()),
            $matches,
        );

        foreach ($matches[2] as $key) {
            if (str_ends_with($key, '/') || str_ends_with($key, '.') || str_contains($key, '$')) {
                continue;
            }

            $keys[$key] = true;
        }
    }

    expect($keys)->not->toBeEmpty();

    $missing = array_values(array_filter(
        array_keys($keys),
        fn (string $key): bool => ! Lang::hasForLocale($key, 'en'),
    ));

    expect($missing)->toBe([]);
});

it('never names the functional reference product in any translation', function () {
    $forbiddenWord = strrev('oodo');

    foreach (['en', 'ar', 'es', 'fr', 'pt_BR'] as $locale) {
        foreach (crmLangFiles($locale) as $relativePath => $path) {
            expect(stripos(File::get($path), $forbiddenWord))->toBeFalse("{$locale}/{$relativePath}.php mentions it");
        }
    }
});
