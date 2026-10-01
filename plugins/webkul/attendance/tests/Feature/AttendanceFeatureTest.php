<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Webkul\Attendance\Filament\Exports\AttendanceExporter;
use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\CreateAttendance;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\EditAttendance;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\ListAttendances;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Schemas\AttendanceForm;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Tables\AttendancesTable;
use Webkul\Attendance\Filament\Widgets\AttendanceStatsWidget;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Policies\AttendancePolicy;
use Webkul\Attendance\Writers\WriterRegistry;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendance');

    WriterRegistry::flush();
    WriterRegistry::register('biometric-attendance');
    WriterRegistry::register('remote');
});

it('creates an attendance record with relations and casts', function () {
    $employee = Employee::factory()->create();

    $attendance = Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'check_out'   => '2026-09-24 17:00:00',
        'source'      => 'manual',
        'company_id'  => $employee->company_id,
    ]);

    expect($attendance->employee->is($employee))->toBeTrue()
        ->and($attendance->work_date->format('Y-m-d'))->toBe('2026-09-24')
        ->and($attendance->check_in->format('Y-m-d H:i:s'))->toBe('2026-09-24 08:00:00')
        ->and($attendance->check_out->format('Y-m-d H:i:s'))->toBe('2026-09-24 17:00:00')
        ->and($attendance->source)->toBe('manual')
        ->and($attendance->worked_minutes)->toBe(540);
});

it('reports null worked minutes when the day is still open', function () {
    $employee = Employee::factory()->create();

    $attendance = Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
    ]);

    expect($attendance->worked_minutes)->toBeNull();
});

it('allows one row per employee, work date and source', function () {
    $employee = Employee::factory()->create();

    Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
    ]);

    Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:10:00',
        'source'      => 'biometric-attendance',
    ]);

    expect(Attendance::where('employee_id', $employee->id)->count())->toBe(2);

    expect(fn () => Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:20:00',
        'source'      => 'manual',
    ]))->toThrow(QueryException::class);
});

it('maps the attendance policy', function () {
    expect(Gate::getPolicyFor(Attendance::class))
        ->toBeInstanceOf(AttendancePolicy::class);
});

it('registers the attendance resource with slug, group and shield config', function () {
    expect(AttendanceResource::getSlug())->toBe('attendance/attendances')
        ->and(AttendanceResource::getNavigationGroup())->toBe(NavigationGroup::Attendance)
        ->and(NavigationGroup::Attendance->getLabel())->toBe('Attendance')
        ->and(AttendanceResource::getModelLabel())->toBe('Attendance')
        ->and(config('filament-shield.resources.manage'))->toHaveKey(AttendanceResource::class);
});

it('aggregates monthly stats for the header widget', function () {
    $month = Carbon::create(2026, 9, 15);

    $employeeA = Employee::factory()->create();
    $employeeB = Employee::factory()->create(['user_id' => User::factory()]);

    Attendance::create([
        'employee_id' => $employeeA->id,
        'work_date'   => '2026-09-10',
        'check_in'    => '2026-09-10 08:00:00',
        'check_out'   => '2026-09-10 16:00:00',
        'source'      => 'manual',
    ]);

    Attendance::create([
        'employee_id' => $employeeA->id,
        'work_date'   => '2026-09-11',
        'check_in'    => '2026-09-11 08:00:00',
        'source'      => 'biometric-attendance',
    ]);

    Attendance::create([
        'employee_id' => $employeeB->id,
        'work_date'   => '2026-09-11',
        'check_in'    => '2026-09-11 09:00:00',
        'check_out'   => '2026-09-11 17:00:00',
        'source'      => 'manual',
    ]);

    Attendance::create([
        'employee_id' => $employeeB->id,
        'work_date'   => '2026-08-20',
        'check_in'    => '2026-08-20 09:00:00',
        'check_out'   => '2026-08-20 17:00:00',
        'source'      => 'manual',
    ]);

    expect(AttendanceStatsWidget::monthlyStats($month))->toBe([
        'records'   => 3,
        'employees' => 2,
        'hours'     => 16.0,
        'open'      => 1,
    ]);
});

it('exposes the expected exporter columns', function () {
    $columns = collect(AttendanceExporter::getColumns())->map->getName()->all();

    expect($columns)->toBe([
        'employee.name',
        'work_date',
        'check_in',
        'check_out',
        'worked_minutes',
        'source',
    ]);
});

it('maps persisted rows to export values on the employee wall clock', function () {
    $riyadhEmployee = Employee::factory()->create([
        'user_id'   => User::factory(),
        'time_zone' => 'Asia/Riyadh',
    ]);

    $closed = Attendance::create([
        'employee_id'  => $riyadhEmployee->id,
        'work_date'    => '2026-09-24',
        'check_in'     => '2026-09-24 05:00:00',
        'check_out'    => '2026-09-24 13:00:00',
        'source'       => 'biometric-attendance:3',
        'source_label' => 'Main Gate',
    ]);

    $open = Attendance::create([
        'employee_id' => $riyadhEmployee->id,
        'work_date'   => '2026-09-25',
        'check_in'    => '2026-09-25 05:00:00',
        'source'      => 'manual',
    ]);

    $columns = AttendanceExporter::getColumns();
    $columnMap = collect($columns)->mapWithKeys(fn ($column) => [$column->getName() => $column->getLabel()])->all();

    $export = new Export(['exporter' => AttendanceExporter::class]);
    $exporter = $export->getExporter($columnMap, []);

    expect($exporter($closed->refresh()))->toBe([
        $riyadhEmployee->name,
        '2026-09-24',
        '2026-09-24 08:00:00',
        '2026-09-24 16:00:00',
        '8h 0m',
        'Main Gate',
    ])->and($exporter($open->refresh()))->toBe([
        $riyadhEmployee->name,
        '2026-09-25',
        '2026-09-25 08:00:00',
        '',
        '',
        'Manual',
    ]);
});

it('renders the create and edit attendance pages', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'view_attendance_attendance',
        'create_attendance_attendance',
        'update_attendance_attendance',
    ]);

    Livewire::test(CreateAttendance::class)->assertOk();

    $attendance = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
    ]);

    Livewire::test(EditAttendance::class, ['record' => $attendance->getRouteKey()])->assertOk();
});

it('only allows completing open rows and fresh manual corrections', function () {
    $user = FilamentHelper::actingAs(['update_attendance_attendance']);

    $employeeId = Employee::factory()->create(['user_id' => User::factory()])->id;

    $today = Carbon::today()->toDateString();
    $oldDate = Carbon::today()->subDays(5)->toDateString();

    $openDeviceRow = Attendance::create([
        'employee_id' => $employeeId,
        'work_date'   => $today,
        'check_in'    => $today.' 08:00:00',
        'source'      => 'biometric-attendance',
    ]);

    $closedDeviceRow = Attendance::create([
        'employee_id' => $employeeId,
        'work_date'   => $oldDate,
        'check_in'    => $oldDate.' 08:00:00',
        'check_out'   => $oldDate.' 17:00:00',
        'source'      => 'biometric-attendance',
    ]);

    $freshManualRow = Attendance::create([
        'employee_id' => $employeeId,
        'work_date'   => $today,
        'check_in'    => $today.' 08:00:00',
        'check_out'   => $today.' 17:00:00',
        'source'      => 'manual',
    ]);

    $oldManualRow = Attendance::create([
        'employee_id' => $employeeId,
        'work_date'   => $oldDate,
        'check_in'    => $oldDate.' 08:00:00',
        'check_out'   => $oldDate.' 17:00:00',
        'source'      => 'manual',
    ]);

    expect($user->can('update', $openDeviceRow))->toBeTrue()
        ->and($user->can('update', $closedDeviceRow))->toBeFalse()
        ->and($user->can('update', $freshManualRow))->toBeTrue()
        ->and($user->can('update', $oldManualRow))->toBeFalse();
});

it('only allows deleting manual rows so old ones are fixed via delete and recreate', function () {
    $user = FilamentHelper::actingAs(['delete_attendance_attendance']);

    $employeeId = Employee::factory()->create(['user_id' => User::factory()])->id;

    $today = Carbon::today()->toDateString();
    $oldDate = Carbon::today()->subDays(5)->toDateString();

    $deviceRow = Attendance::create([
        'employee_id' => $employeeId,
        'work_date'   => $today,
        'check_in'    => $today.' 08:00:00',
        'check_out'   => $today.' 17:00:00',
        'source'      => 'biometric-attendance',
    ]);

    $freshManualRow = Attendance::create([
        'employee_id' => $employeeId,
        'work_date'   => $today,
        'check_in'    => $today.' 08:00:00',
        'source'      => 'manual',
    ]);

    $oldManualRow = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => $oldDate,
        'check_in'    => $oldDate.' 08:00:00',
        'source'      => 'manual',
    ]);

    expect($user->can('delete', $deviceRow))->toBeFalse()
        ->and($user->can('delete', $freshManualRow))->toBeTrue()
        ->and($user->can('delete', $oldManualRow))->toBeTrue();
});

it('stamps the creator on save without breaking unauthenticated creates', function () {
    $user = FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
    ]);

    $employee = Employee::factory()->create(['user_id' => User::factory()]);

    $attendance = Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
    ]);

    expect($attendance->creator_id)->toBe($user->id);
});

it('validates the create form: duplicate, out-of-day check-in and overlong shifts', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
    ]);

    $employee = Employee::factory()->create(['user_id' => User::factory()]);
    $today = Carbon::today()->toDateString();

    Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => $today,
        'check_in'    => $today.' 08:00:00',
        'source'      => 'manual',
    ]);

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'work_date'   => $today,
            'check_in'    => $today.' 09:00:00',
        ])
        ->call('create')
        ->assertHasFormErrors(['work_date']);

    $otherEmployeeId = Employee::factory()->create(['user_id' => User::factory()])->id;
    $yesterday = Carbon::today()->subDay()->toDateString();

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $otherEmployeeId,
            'work_date'   => $today,
            'check_in'    => $today.' 08:00:00',
        ])
        ->set('data.work_date', $yesterday)
        ->call('create')
        ->assertHasFormErrors(['check_in']);

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
            'work_date'   => $today,
            'check_in'    => $today.' 00:00:00',
            'check_out'   => $today.' 17:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $happyEmployeeId = Employee::factory()->create(['user_id' => User::factory()])->id;

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $happyEmployeeId,
            'work_date'   => $today,
            'check_in'    => $today.' 08:00:00',
            'check_out'   => $today.' 16:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Attendance::where('employee_id', $happyEmployeeId)->where('work_date', $today)->whereNotNull('check_out')->count())->toBe(1);
});

it('derives the work date from the check-in in the employee time zone', function () {
    expect(AttendanceForm::resolveWorkDate('2026-09-24 08:00:00', null))->toBe('2026-09-24');

    $riyadhEmployee = Employee::factory()->create([
        'user_id'   => User::factory(),
        'time_zone' => 'Asia/Riyadh',
    ]);

    expect(AttendanceForm::resolveWorkDate('2026-09-24 00:30:00', $riyadhEmployee->id))->toBe('2026-09-24');

    $kiritimatiEmployee = Employee::factory()->create([
        'user_id'   => User::factory(),
        'time_zone' => 'Pacific/Kiritimati',
    ]);

    expect(AttendanceForm::resolveWorkDate('2026-09-24 00:30:00', $kiritimatiEmployee->id))->toBe('2026-09-24')
        ->and(AttendanceForm::resolveWorkDate('2026-09-23 09:30:00', $kiritimatiEmployee->id))->toBe('2026-09-23');
});

it('normalizes manual wall clocks to UTC on save', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
    ]);

    $employee = Employee::factory()->create([
        'user_id'   => User::factory(),
        'time_zone' => 'Asia/Riyadh',
    ]);

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'work_date'   => '2026-09-24',
            'check_in'    => '2026-09-24 08:00:00',
            'check_out'   => '2026-09-24 16:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $stored = Attendance::where('employee_id', $employee->id)->first();

    expect($stored->getAttributes()['check_in'])->toBe('2026-09-24 05:00:00')
        ->and($stored->getAttributes()['check_out'])->toBe('2026-09-24 13:00:00')
        ->and($stored->work_date->format('Y-m-d'))->toBe('2026-09-24')
        ->and($stored->worked_minutes)->toBe(480);
});

it('accepts the employee-local today past UTC midnight', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
    ]);

    Carbon::setTestNow('2026-09-29 21:30:00');

    try {
        $employee = Employee::factory()->create([
            'user_id'   => User::factory(),
            'time_zone' => 'Asia/Riyadh',
        ]);

        expect(AttendanceForm::employeeToday($employee->id))->toBe('2026-09-30');

        Livewire::test(CreateAttendance::class)
            ->fillForm([
                'employee_id' => $employee->id,
                'work_date'   => '2026-09-30',
                'check_in'    => '2026-09-30 00:30:00',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Attendance::where('employee_id', $employee->id)->where('work_date', '2026-09-30')->count())->toBe(1);
    } finally {
        Carbon::setTestNow();
    }
});

it('displays the employee wall clock from raw UTC when the app timezone is not UTC', function () {
    $employee = Employee::factory()->create([
        'user_id'   => User::factory(),
        'time_zone' => 'Asia/Riyadh',
    ]);

    Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 05:00:00',
        'source'      => 'manual',
    ]);

    config()->set('app.timezone', 'Asia/Riyadh');

    try {
        $attendance = Attendance::where('employee_id', $employee->id)->firstOrFail();

        expect(AttendancesTable::employeeWallClock($attendance, 'check_in')?->format('Y-m-d H:i:s'))
            ->toBe('2026-09-24 08:00:00');
    } finally {
        config()->set('app.timezone', 'UTC');
    }
});

it('ignores a forged source value and always stores manual rows from the form', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
    ]);

    $employee = Employee::factory()->create(['user_id' => User::factory()]);

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'work_date'   => Carbon::today()->toDateString(),
            'check_in'    => Carbon::today()->toDateString().' 08:00:00',
            'source'      => 'biometric-attendance',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Attendance::where('employee_id', $employee->id)->firstOrFail()->source)->toBe('manual');
});

it('keeps stale open device rows completable instead of locking them', function () {
    $user = FilamentHelper::actingAs(['update_attendance_attendance']);

    $oldDate = Carbon::today()->subDays(40)->toDateString();

    $staleOpenRow = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => $oldDate,
        'check_in'    => $oldDate.' 08:00:00',
        'source'      => 'biometric-attendance',
    ]);

    expect($user->can('update', $staleOpenRow))->toBeTrue();
});

it('scopes group users to their allowed companies instead of crashing', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $user = CompanyHelper::actingAsCompanyUser($companyA, [
        'view_any_attendance_attendance',
        'update_attendance_attendance',
        'delete_attendance_attendance',
    ]);
    $user->forceFill(['resource_permission' => PermissionType::GROUP])->saveQuietly();

    $today = Carbon::today()->toDateString();

    $own = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => $today,
        'check_in'    => $today.' 08:00:00',
        'check_out'   => $today.' 17:00:00',
        'source'      => 'manual',
        'company_id'  => $companyA->id,
    ]);

    $foreign = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => $today,
        'check_in'    => $today.' 08:00:00',
        'check_out'   => $today.' 17:00:00',
        'source'      => 'manual',
        'company_id'  => $companyB->id,
    ]);

    expect($user->can('update', $own))->toBeTrue()
        ->and($user->can('delete', $own))->toBeTrue()
        ->and($user->can('update', $foreign))->toBeFalse()
        ->and($user->can('delete', $foreign))->toBeFalse();
});

it('floors partial minutes instead of rounding up', function () {
    $attendance = Attendance::create([
        'employee_id' => Employee::factory()->create()->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'check_out'   => '2026-09-24 16:00:30',
        'source'      => 'manual',
    ]);

    expect($attendance->worked_minutes)->toBe(480);
});

it('hides attendance rows owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $own = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
        'company_id'  => $companyA->id,
    ]);

    $other = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
        'company_id'  => $companyB->id,
    ]);

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = Attendance::query()->pluck('id');

    expect($visible)->toContain($own->id)
        ->not->toContain($other->id);
});

it('stamps a new attendance row with the active company', function () {
    $company = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($company);

    $attendance = Attendance::create([
        'employee_id' => Employee::factory()->create()->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
    ]);

    expect($attendance->company_id)->toBe($company->id);
});

it('rejects malformed sources on direct writes', function () {
    $employeeId = Employee::factory()->create(['user_id' => User::factory()])->id;

    foreach (['has space', 'biometric-attendance:', 'UPPER', 'a:b:c', 'remote:sanaa-team', 'ghost', 'ghost:5'] as $source) {
        expect(fn () => Attendance::create([
            'employee_id' => $employeeId,
            'work_date'   => '2026-09-24',
            'check_in'    => '2026-09-24 08:00:00',
            'source'      => $source,
        ]))->toThrow(InvalidArgumentException::class);
    }
});

it('accepts namespaced writer keys with human labels', function () {
    $employeeId = Employee::factory()->create(['user_id' => User::factory()])->id;

    $deviceRow = Attendance::create([
        'employee_id'  => $employeeId,
        'work_date'    => '2026-09-24',
        'check_in'     => '2026-09-24 08:00:00',
        'source'       => 'biometric-attendance:3',
        'source_label' => 'Main Gate',
    ]);

    $remoteRow = Attendance::create([
        'employee_id'  => $employeeId,
        'work_date'    => '2026-09-25',
        'check_in'     => '2026-09-25 08:00:00',
        'source'       => 'remote',
        'source_label' => 'Sanaa Team',
    ]);

    expect($deviceRow->source_label)->toBe('Main Gate')
        ->and(Attendance::sourceDisplayName('biometric-attendance:3', 'Main Gate'))->toBe('Main Gate')
        ->and(Attendance::sourceDisplayName('biometric-attendance:3'))->toBe('Biometric Attendance 3')
        ->and(Attendance::sourceDisplayName('remote', 'Sanaa Team'))->toBe('Sanaa Team')
        ->and($remoteRow->source)->toBe('remote');
});

it('resolves writer labels live and parses writer keys', function () {
    WriterRegistry::register('gate', fn (?int $ref) => $ref ? "Gate {$ref}" : 'Gates');

    expect(Attendance::sourceWriter('gate:3'))->toBe('gate')
        ->and(Attendance::sourceReferenceId('gate:3'))->toBe(3)
        ->and(Attendance::sourceWriter('remote'))->toBe('remote')
        ->and(Attendance::sourceReferenceId('remote'))->toBeNull()
        ->and(Attendance::sourceWriter('has space'))->toBeNull()
        ->and(Attendance::sourceReferenceId('has space'))->toBeNull()
        ->and(Attendance::sourceDisplayName('gate:3', 'Old snapshot'))->toBe('Gate 3')
        ->and(Attendance::sourceDisplayName('remote'))->toBe('Remote');
});

it('supports bare writer keys with no reference', function () {
    expect(Attendance::isValidSource('remote'))->toBeTrue()
        ->and(Attendance::isValidSource('manual'))->toBeTrue()
        ->and(Attendance::isValidSource('ghost'))->toBeFalse();

    $attendance = Attendance::create([
        'employee_id' => Employee::factory()->create()->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'remote',
    ]);

    expect($attendance->source)->toBe('remote')
        ->and(Attendance::where('source', 'remote')->count())->toBe(1);
});

it('accepts hyphenated writer slugs with numeric references', function () {
    WriterRegistry::register('biometric-attendance', fn (?int $ref) => $ref ? "Device {$ref}" : 'Devices');

    expect(Attendance::isValidSource('biometric-attendance:3'))->toBeTrue()
        ->and(Attendance::sourceWriter('biometric-attendance:3'))->toBe('biometric-attendance')
        ->and(Attendance::sourceReferenceId('biometric-attendance:3'))->toBe(3)
        ->and(Attendance::sourceDisplayName('biometric-attendance:3'))->toBe('Device 3');

    $attendance = Attendance::create([
        'employee_id'  => Employee::factory()->create()->id,
        'work_date'    => '2026-09-24',
        'check_in'     => '2026-09-24 08:00:00',
        'source'       => 'biometric-attendance:3',
        'source_label' => 'Main Gate',
    ]);

    expect($attendance->source)->toBe('biometric-attendance:3');
});

it('rejects invalid writer slugs at registration time', function () {
    foreach (['has space', '-foo', 'foo-', 'UPPER-'] as $slug) {
        expect(fn () => WriterRegistry::register($slug))->toThrow(InvalidArgumentException::class);
    }

    expect(WriterRegistry::isRegistered('has space'))->toBeFalse()
        ->and(Attendance::isValidSource('-foo:3'))->toBeFalse()
        ->and(Attendance::sourceWriter('-foo:3'))->toBeNull();
});

it('falls back to the employee company when no company context is active', function () {
    $attendance = CompanyHelper::withoutCompanyContext(fn () => Attendance::create([
        'employee_id' => Employee::factory()->create()->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
    ]));

    expect($attendance->company_id)->toBe($attendance->employee->company_id)
        ->and($attendance->company_id)->not->toBeNull();
});

it('keeps the row identity on edit even when the request forges it', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'update_attendance_attendance',
    ]);

    $employee = Employee::factory()->create([
        'user_id'   => User::factory(),
        'time_zone' => 'Asia/Riyadh',
    ]);

    $attendance = Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 05:00:00',
        'source'      => 'manual',
    ]);

    $rawCheckIn = $attendance->getRawOriginal('check_in');

    Livewire::test(EditAttendance::class, ['record' => $attendance->getRouteKey()])
        ->set('data.employee_id', Employee::factory()->create(['user_id' => User::factory()])->id)
        ->call('save')
        ->assertHasNoFormErrors();

    $attendance->refresh();

    expect($attendance->employee_id)->toBe($employee->id)
        ->and($attendance->work_date->format('Y-m-d'))->toBe('2026-09-24')
        ->and($attendance->getRawOriginal('check_in'))->toBe($rawCheckIn);
});

it('rejects a check-out outside the work date', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
    ]);

    $employee = Employee::factory()->create(['user_id' => User::factory()]);

    $today = Carbon::today()->toDateString();
    $tomorrow = Carbon::tomorrow()->toDateString();

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'work_date'   => $today,
            'check_in'    => $today.' 22:00:00',
            'check_out'   => $tomorrow.' 06:00:00',
        ])
        ->call('create')
        ->assertHasFormErrors(['check_out']);

    expect(Attendance::where('employee_id', $employee->id)->count())->toBe(0);
});

it('fills the work date from the employee timezone when picking an employee', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
    ]);

    Carbon::setTestNow('2026-09-29 21:30:00');

    try {
        $employee = Employee::factory()->create([
            'user_id'   => User::factory(),
            'time_zone' => 'Asia/Riyadh',
        ]);

        Livewire::test(CreateAttendance::class)
            ->set('data.employee_id', $employee->id)
            ->assertFormSet(['work_date' => '2026-09-30']);
    } finally {
        Carbon::setTestNow();
    }
});

it('completes an open row through the checkout action storing UTC', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'update_attendance_attendance',
    ]);

    $employee = Employee::factory()->create([
        'user_id'   => User::factory(),
        'time_zone' => 'Asia/Riyadh',
    ]);

    $today = Carbon::today()->toDateString();

    $attendance = Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => $today,
        'check_in'    => $today.' 05:00:00',
        'source'      => 'manual',
    ]);

    Livewire::test(ListAttendances::class)
        ->callTableAction('add_checkout', $attendance, data: ['check_out' => $today.' 16:00:00'])
        ->assertHasNoErrors();

    $attendance->refresh();

    expect($attendance->getRawOriginal('check_out'))->toBe($today.' 13:00:00')
        ->and($attendance->worked_minutes)->toBe(480);
});

it('rejects a next-day check-out through the checkout action', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'update_attendance_attendance',
    ]);

    $employee = Employee::factory()->create(['user_id' => User::factory()]);

    $today = Carbon::today()->toDateString();
    $tomorrow = Carbon::tomorrow()->toDateString();

    $attendance = Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => $today,
        'check_in'    => $today.' 22:00:00',
        'source'      => 'manual',
    ]);

    Livewire::test(ListAttendances::class)
        ->callTableAction('add_checkout', $attendance, data: ['check_out' => $tomorrow.' 06:00:00'])
        ->assertHasErrors(['check_out']);

    expect($attendance->refresh()->check_out)->toBeNull();
});

it('hides the checkout action without the update permission', function () {
    FilamentHelper::actingAs(['view_any_attendance_attendance']);

    $attendance = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => '2026-09-24',
        'check_in'    => '2026-09-24 08:00:00',
        'source'      => 'manual',
    ]);

    Livewire::test(ListAttendances::class)
        ->assertTableActionHidden('add_checkout', $attendance);
});

it('rejects a work date in the future', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
    ]);

    $employee = Employee::factory()->create(['user_id' => User::factory()]);

    $tomorrow = Carbon::tomorrow()->toDateString();

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'work_date'   => $tomorrow,
            'check_in'    => $tomorrow.' 08:00:00',
        ])
        ->call('create')
        ->assertHasFormErrors(['work_date']);
});

it('filters rows by the current-month preset', function () {
    FilamentHelper::actingAs(['view_any_attendance_attendance']);

    $september = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => '2026-09-10',
        'check_in'    => '2026-09-10 08:00:00',
        'source'      => 'manual',
    ]);

    $august = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => '2026-08-20',
        'check_in'    => '2026-08-20 08:00:00',
        'source'      => 'manual',
    ]);

    Carbon::setTestNow('2026-09-24 12:00:00');

    try {
        Livewire::test(ListAttendances::class)
            ->filterTable('work_date', ['preset' => 'this_month'])
            ->assertCanSeeTableRecords([$september])
            ->assertCanNotSeeTableRecords([$august]);
    } finally {
        Carbon::setTestNow();
    }
});

it('filters rows by writer source', function () {
    FilamentHelper::actingAs(['view_any_attendance_attendance']);

    $manual = Attendance::create([
        'employee_id' => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'   => '2026-09-10',
        'check_in'    => '2026-09-10 08:00:00',
        'source'      => 'manual',
    ]);

    $device = Attendance::create([
        'employee_id'  => Employee::factory()->create(['user_id' => User::factory()])->id,
        'work_date'    => '2026-09-10',
        'check_in'     => '2026-09-10 08:00:00',
        'source'       => 'biometric-attendance:3',
        'source_label' => 'Main Gate',
    ]);

    Livewire::test(ListAttendances::class)
        ->filterTable('source', 'biometric-attendance:3')
        ->assertCanSeeTableRecords([$device])
        ->assertCanNotSeeTableRecords([$manual]);
});

it('saves an untouched edit without drifting the stored times', function () {
    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'update_attendance_attendance',
    ]);

    $today = Carbon::today()->toDateString();

    $attendance = Attendance::create([
        'employee_id' => Employee::factory()->create([
            'user_id'   => User::factory(),
            'time_zone' => 'Asia/Riyadh',
        ])->id,
        'work_date'   => $today,
        'check_in'    => $today.' 05:00:00',
        'check_out'   => $today.' 13:00:00',
        'source'      => 'manual',
    ]);

    $rawBefore = [$attendance->getRawOriginal('check_in'), $attendance->getRawOriginal('check_out')];

    Livewire::test(EditAttendance::class, ['record' => $attendance->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    $attendance->refresh();

    expect([$attendance->getRawOriginal('check_in'), $attendance->getRawOriginal('check_out')])->toBe($rawBefore);
});
