<?php

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Employee\Enums\DistanceUnit;
use Webkul\Employee\Enums\Gender;
use Webkul\Employee\Enums\MaritalStatus;
use Webkul\Employee\Models\Employee;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');
});

it('builds an employee from its factory', function () {
    $employee = Employee::factory()->create();

    expect($employee->exists)->toBeTrue()
        ->and($employee->name)->not->toBeEmpty();
});

it('only writes values the enum-backed columns accept', function () {
    $employee = Employee::factory()->create();

    expect(Gender::tryFrom($employee->gender))->not->toBeNull()
        ->and(DistanceUnit::tryFrom($employee->distance_home_work_unit))->not->toBeNull()
        ->and(MaritalStatus::tryFrom($employee->marital))->not->toBeNull();
});

it('resolves the employment type relation', function () {
    $employee = Employee::factory()->create();

    expect($employee->employmentType)->not->toBeNull()
        ->and($employee->employmentType->name)->not->toBeEmpty();
});
