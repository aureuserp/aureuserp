<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

$namespace = 'Webkul\\Purchase\\';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');

    SecurityHelper::disableUserEvents();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('registers resources that build their own global search details', function () use ($namespace) {
    expect(FilamentHelper::resourcesWithOwnGlobalSearchDetails($namespace))->not->toBeEmpty();
});

it('builds global search details for a record whose optional relations are empty', function () use ($namespace) {
    $failures = [];

    foreach (FilamentHelper::resourcesWithOwnGlobalSearchDetails($namespace) as $resource) {
        $model = $resource::getModel();

        if (! is_subclass_of($model, Model::class)) {
            continue;
        }

        try {
            $resource::getGlobalSearchResultDetails(new $model);
        } catch (Throwable $exception) {
            $failures[] = $resource.' => '.$exception->getMessage();
        }
    }

    expect($failures)->toBe([]);
});

it('builds a global search title for a record whose optional relations are empty', function () use ($namespace) {
    $failures = [];

    foreach (FilamentHelper::resourcesWithOwnGlobalSearchDetails($namespace) as $resource) {
        $model = $resource::getModel();

        if (! is_subclass_of($model, Model::class)) {
            continue;
        }

        try {
            $resource::getGlobalSearchResultTitle(new $model);
        } catch (Throwable $exception) {
            $failures[] = $resource.' => '.$exception->getMessage();
        }
    }

    expect($failures)->toBe([]);
});
