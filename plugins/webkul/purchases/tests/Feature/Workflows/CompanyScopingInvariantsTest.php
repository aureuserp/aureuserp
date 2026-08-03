<?php

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyScopeHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

$plugin = 'purchases';

$shared = [];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
});

it('finds the models of this plugin that opt into company scoping', function () use ($plugin) {
    expect(CompanyScopeHelper::companyModels($plugin))->not->toBeEmpty();
});

it('stamps the active company on every model that is not declared shared', function () use ($plugin, $shared) {
    expect(CompanyScopeHelper::unexpectedlyShared($plugin, $shared))->toBe([]);
});
