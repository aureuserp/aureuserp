<?php

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/EmployeeHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');
});

it('builds a resume from its factory', function () {
    $resume = EmployeeHelper::resume();

    expect($resume->exists)->toBeTrue()
        ->and($resume->name)->not->toBeEmpty()
        ->and($resume->display_type)->not->toBeNull();
});

it('associates the resume with its employee and type', function () {
    $employee = EmployeeHelper::employee(['name' => 'Ada Lovelace']);

    $resume = EmployeeHelper::resume([], $employee);

    expect($resume->employee->name)->toBe('Ada Lovelace')
        ->and($resume->resumeType)->not->toBeNull();
});
