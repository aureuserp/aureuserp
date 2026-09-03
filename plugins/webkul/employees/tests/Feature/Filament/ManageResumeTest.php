<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageResume;
use Webkul\Employee\Models\EmployeeResumeAttachment;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/EmployeeHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');

    // The page renders links to sibling resources, and those named routes are not
    // registered when only this plugin is installed. Same guard the accounting
    // Filament tests use.
    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs([
        'view_any_employee_employee',
        'view_employee_employee',
        'update_employee_employee',
    ]);
});

it('renders the manage resume page for an employee', function () {
    $employee = EmployeeHelper::employee();

    Livewire::test(ManageResume::class, ['record' => $employee->id])
        ->assertOk();
});

it('lists a resume line and its attachment count on the page', function () {
    $employee = EmployeeHelper::employee();

    $resume = EmployeeHelper::resume(['name' => 'Senior Engineer'], $employee);

    $resume->attachments()->create([
        'file_path'          => EmployeeResumeAttachment::UPLOAD_DIRECTORY.'/cv.pdf',
        'original_file_name' => 'cv.pdf',
    ]);

    Livewire::test(ManageResume::class, ['record' => $employee->id])
        ->assertOk()
        ->assertCanSeeTableRecords([$resume]);
});

it('renders the create form including the attachments repeater', function () {
    $employee = EmployeeHelper::employee();

    Livewire::test(ManageResume::class, ['record' => $employee->id])
        ->mountAction('create')
        ->assertOk();
});

it('renders the view modal for a resume carrying an attachment', function () {
    $employee = EmployeeHelper::employee();

    $resume = EmployeeHelper::resume([], $employee);

    $resume->attachments()->create([
        'file_path'          => EmployeeResumeAttachment::UPLOAD_DIRECTORY.'/cv.pdf',
        'original_file_name' => 'cv.pdf',
    ]);

    Livewire::test(ManageResume::class, ['record' => $employee->id])
        ->mountAction('view', arguments: ['record' => $resume->getKey()])
        ->assertOk();
});
