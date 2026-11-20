<?php

use Filament\Actions\Testing\TestAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\AccountingSetupResult;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageDefaultAccounts;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');

    DB::table('plugins')->whereIn('name', ['accounts', 'accounting'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['page_accounting_manage_default_accounts']);
});

function mockAccountingSetup(Closure $setUp): void
{
    $mock = Mockery::mock(AccountingSetupService::class);

    $mock->shouldReceive('isSetUp')->andReturn(false);

    $setUp($mock->shouldReceive('setUp')->once());

    app()->instance(AccountingSetupService::class, $mock);
}

function callAccountingSetupAction()
{
    return Livewire::test(ManageDefaultAccounts::class)
        ->callAction(TestAction::make('setupAccounting')->schemaComponent('accountingNotSetUp', schema: 'form'));
}

it('shows a notification that matches the setup result', function (AccountingSetupResult $result, string $key, string $status) {
    mockAccountingSetup(fn ($expectation) => $expectation->andReturn($result));

    callAccountingSetupAction()
        ->assertNotified(
            Notification::make()
                ->status($status)
                ->title(__('accounts::enums/accounting-setup-result.'.$key))
                ->body(__('accounts::enums/accounting-setup-result.descriptions.'.$key))
        )
        ->assertRedirect();
})->with([
    'completed'      => [AccountingSetupResult::COMPLETED, 'completed', 'success'],
    'already set up' => [AccountingSetupResult::ALREADY_SET_UP, 'already-set-up', 'info'],
    'no template'    => [AccountingSetupResult::NO_TEMPLATE, 'no-template', 'danger'],
    'incomplete'     => [AccountingSetupResult::INCOMPLETE, 'incomplete', 'danger'],
]);

it('shows a failure notification instead of an error page when the setup throws', function () {
    mockAccountingSetup(fn ($expectation) => $expectation->andThrow(new RuntimeException('Setup exploded.')));

    callAccountingSetupAction()
        ->assertNotified(
            Notification::make()
                ->danger()
                ->title(__('accounting::setup.results.failed.title'))
                ->body(__('accounting::setup.results.failed.body'))
        )
        ->assertRedirect();
});
