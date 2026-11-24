<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Employee\Enums\MaritalStatus;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\CreateEmployee;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ViewEmployee;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmploymentType;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/EmployeeHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
    FilamentHelper::actingAs([
        'view_any_employee_employee',
        'view_employee_employee',
        'create_employee_employee',
        'update_employee_employee',
    ]);
});

it('creates an employee with or without employment type and renders private info entries', function () {
    $type = EmploymentType::factory()->create(['name' => 'Full-Time Test']);

    Livewire::test(CreateEmployee::class)
        ->fillForm(['name' => 'Tariq Ali', 'employee_type' => $type->id])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreateEmployee::class)
        ->fillForm(['name' => 'Salma Hassan'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect((int) Employee::where('name', 'Tariq Ali')->value('employee_type'))->toBe($type->id)
        ->and(Employee::where('name', 'Salma Hassan')->value('employee_type'))->toBeNull();

    $employee = EmployeeHelper::employee([
        'employee_type'        => $type->id,
        'marital'              => MaritalStatus::Married->value,
        'spouse_complete_name' => 'Kareem Omar',
        'children'             => 2,
        'certificate'          => 'master',
        'study_field'          => 'Computer Science',
        'study_school'         => 'Aden University',
    ]);

    Livewire::test(ViewEmployee::class, ['record' => $employee->getRouteKey()])
        ->assertOk()
        ->assertSee('Full-Time Test')
        ->assertSee('Kareem Omar')
        ->assertSee('Computer Science')
        ->assertSee('Aden University');
});
