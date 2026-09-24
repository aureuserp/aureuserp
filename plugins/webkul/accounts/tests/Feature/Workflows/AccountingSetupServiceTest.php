<?php

/*
 * Scope (#1573, minimal fix only).
 *
 * What this file proves:
 *  - A self-template (`default_company_id` pointing at the target itself)
 *    makes `setUp()` return false and copy nothing (no journals).
 *  - Skipping setup is reported (warning log with company/template ids)
 *    instead of failing silently.
 *  - A healthy template still provisions (`true` + sale/purchase journals).
 *
 * What it deliberately does NOT prove:
 *  - `setUp() === true` does NOT mean every accounting setting now exists.
 *    It only means the current setup path ran under its current conditions
 *    (a template with journals but no settings still yields `true`).
 *    Template-completeness validation is a separate follow-up, not #1573.
 *  - The Filament success/danger toast branching lives in
 *    `GatesAccountingSetup::setupAction()` and is verified by code review
 *    of that closure, not by a browser/Livewire test.
 *
 * Out of scope here: `isSetUp()`, `DefaultAccountSettings`,
 * `AccountProductSchema`, template selection, auto-provisioning.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Webkul\Account\Models\Journal;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
});

function pointGlobalDefaultCompanyAt(Company $company): void
{
    DB::table('settings')
        ->where('group', 'general')
        ->where('name', 'default_company_id')
        ->delete();

    DB::table('settings')->insert([
        'group'      => 'general',
        'name'       => 'default_company_id',
        'company_id' => null,
        'payload'    => json_encode($company->id),
        'locked'     => false,
    ]);
}

function companyJournalCount(Company $company): int
{
    return DB::table('accounts_journals')->where('company_id', $company->id)->count();
}

it('returns false and provisions nothing when the template company resolves to the target itself', function () {
    $target = Company::factory()->create();

    pointGlobalDefaultCompanyAt($target);

    expect(app(AccountingSetupService::class)->isSetUp($target))->toBeFalse();

    $result = app(AccountingSetupService::class)->setUp($target);

    expect($result)->toBeFalse()
        ->and(companyJournalCount($target))->toBe(0);
});

it('reports why the setup was skipped instead of failing silently', function () {
    $handler = new TestHandler;

    Log::channel()->getLogger()->pushHandler($handler);

    $target = Company::factory()->create();

    pointGlobalDefaultCompanyAt($target);

    app(AccountingSetupService::class)->setUp($target);

    $warnings = array_filter(
        $handler->getRecords(),
        function ($record) use ($target) {
            $level = $record['level'] ?? null;
            $level = $level instanceof BackedEnum ? $level->value : $level;

            return $level === Level::Warning->value
                && ($record['context']['company_id'] ?? null) === $target->id;
        }
    );

    expect($warnings)->not->toBeEmpty();
});

it('provisions sale and purchase journals from another company template', function () {
    $template = Company::factory()->create();

    Journal::factory()->sale()->company($template)->create();
    Journal::factory()->purchase()->company($template)->create();

    $target = Company::factory()->create();

    pointGlobalDefaultCompanyAt($template);

    $result = app(AccountingSetupService::class)->setUp($target);

    expect($result)->toBeTrue()
        ->and(app(AccountingSetupService::class)->isSetUp($target))->toBeTrue();
});
