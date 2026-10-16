<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Webkul\Attendance\Console\ProcessEventsCommand;
use Webkul\Attendance\Events\EventGateway;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceEvent;
use Webkul\Attendance\Writers\WriterRegistry;
use Webkul\Employee\Models\Employee;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendance');

    WriterRegistry::flush();
    WriterRegistry::register('biometric-attendance');
    WriterRegistry::register('remote');
});

it('rejects pushes from unregistered writer slugs', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    expect(fn () => EventGateway::push($employee->id, '2026-10-01 08:00:00', 0, 'ghost', 'evt-1'))
        ->toThrow(InvalidArgumentException::class);
});

it('stores a single event for double pushes of the same source reference', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 08:00:00', 1, 'biometric-attendance:3', 'evt-1', 'Main Gate');
    EventGateway::push($employee->id, '2026-10-01 08:00:00', 1, 'biometric-attendance:3', 'evt-1', 'Main Gate');

    expect(AttendanceEvent::where('source', 'biometric-attendance:3')->where('source_ref', 'evt-1')->count())->toBe(1);
});

it('pairs an overnight shift into one row anchored to its start day', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 22:00:00', 1, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-02 06:00:00', -1, 'biometric-attendance:3', 'evt-2');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->work_date->format('Y-m-d'))->toBe('2026-10-01')
        ->and($rows->first()->getRawOriginal('check_in'))->toBe('2026-10-01 22:00:00')
        ->and($rows->first()->getRawOriginal('check_out'))->toBe('2026-10-02 06:00:00');
});

it('opens a new row for a punch outside any open window instead of merging', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 22:00:00', 1, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-02 06:00:00', -1, 'biometric-attendance:3', 'evt-2');
    EventGateway::push($employee->id, '2026-10-03 08:00:00', 1, 'biometric-attendance:3', 'evt-3');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->orderBy('work_date')->get();

    expect($rows)->toHaveCount(2)
        ->and($rows[0]->work_date->format('Y-m-d'))->toBe('2026-10-01')
        ->and($rows[1]->work_date->format('Y-m-d'))->toBe('2026-10-03');
});

it('fills the event company from the employee company', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    $event = EventGateway::push($employee->id, '2026-10-01 08:00:00', 1, 'biometric-attendance:3', 'evt-1');

    expect($event->company_id)->toBe($employee->company_id)
        ->and($event->company_id)->not->toBeNull();
});

it('produces identical rows when processing the same events twice', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 22:00:00', 1, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-02 06:00:00', -1, 'biometric-attendance:3', 'evt-2');

    Artisan::call('attendance:process-events');
    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->getRawOriginal('check_out'))->toBe('2026-10-02 06:00:00')
        ->and(AttendanceEvent::whereNull('attendance_id')->count())->toBe(0);
});

it('pairs in-gate and out-gate devices of the same writer onto one row', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    $inEvent = EventGateway::push($employee->id, '2026-10-01 22:00:00', 1, 'biometric-attendance:1', 'evt-1', 'In Gate');
    $outEvent = EventGateway::push($employee->id, '2026-10-02 06:00:00', -1, 'biometric-attendance:2', 'evt-2', 'Out Gate');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->work_date->format('Y-m-d'))->toBe('2026-10-01')
        ->and($rows->first()->source)->toBe('biometric-attendance:1')
        ->and($rows->first()->getRawOriginal('check_in'))->toBe('2026-10-01 22:00:00')
        ->and($rows->first()->getRawOriginal('check_out'))->toBe('2026-10-02 06:00:00')
        ->and($inEvent->refresh()->source)->toBe('biometric-attendance:1')
        ->and($outEvent->refresh()->source)->toBe('biometric-attendance:2')
        ->and($inEvent->attendance_id)->toBe($rows->first()->id)
        ->and($outEvent->attendance_id)->toBe($rows->first()->id);
});

it('opens a new row for a punch 33 hours later instead of merging a forgotten checkout', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 08:00:00', 1, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-02 17:00:00', 1, 'biometric-attendance:3', 'evt-2');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->orderBy('work_date')->get();

    expect($rows)->toHaveCount(2)
        ->and($rows[0]->work_date->format('Y-m-d'))->toBe('2026-10-01')
        ->and($rows[0]->check_out)->toBeNull()
        ->and($rows[1]->work_date->format('Y-m-d'))->toBe('2026-10-02');
});

it('accepts a punch exactly 16 hours after check-in onto the same row', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 08:00:00', 1, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-02 00:00:00', -1, 'biometric-attendance:3', 'evt-2');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->work_date->format('Y-m-d'))->toBe('2026-10-01')
        ->and($rows->first()->getRawOriginal('check_out'))->toBe('2026-10-02 00:00:00');
});

it('keeps a night shift on one row when a mid-shift punch arrives', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 22:00:00', 0, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-02 02:00:00', 0, 'biometric-attendance:3', 'evt-2');
    EventGateway::push($employee->id, '2026-10-02 06:00:00', 0, 'biometric-attendance:3', 'evt-3');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->work_date->format('Y-m-d'))->toBe('2026-10-01')
        ->and($rows->first()->getRawOriginal('check_in'))->toBe('2026-10-01 22:00:00')
        ->and($rows->first()->getRawOriginal('check_out'))->toBe('2026-10-02 06:00:00');
});

it('ignores an accidental double scan so the real checkout still lands on the row', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 22:00:00', 0, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-01 22:00:40', 0, 'biometric-attendance:3', 'evt-2');
    EventGateway::push($employee->id, '2026-10-02 06:00:00', 0, 'biometric-attendance:3', 'evt-3');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->getRawOriginal('check_in'))->toBe('2026-10-01 22:00:00')
        ->and($rows->first()->getRawOriginal('check_out'))->toBe('2026-10-02 06:00:00')
        ->and(AttendanceEvent::where('employee_id', $employee->id)->whereNull('attendance_id')->count())->toBe(0);
});

it('keeps a row open when the only second punch is a double scan', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 08:00:00', 0, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-01 08:01:00', 0, 'biometric-attendance:3', 'evt-2');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->check_out)->toBeNull();
});

it('moves check-in earlier when an older punch arrives in a later run', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 09:00:00', 0, 'biometric-attendance:3', 'evt-2');
    EventGateway::push($employee->id, '2026-10-01 17:00:00', 0, 'biometric-attendance:3', 'evt-3');

    Artisan::call('attendance:process-events');

    EventGateway::push($employee->id, '2026-10-01 08:00:00', 0, 'biometric-attendance:3', 'evt-1');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->getRawOriginal('check_in'))->toBe('2026-10-01 08:00:00')
        ->and($rows->first()->getRawOriginal('check_out'))->toBe('2026-10-01 17:00:00')
        ->and(AttendanceEvent::where('employee_id', $employee->id)->whereNull('attendance_id')->count())->toBe(0);
});

it('stores two device sessions on the same work date when punches are more than 16 hours apart', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 00:10:00', 0, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-01 16:40:00', 0, 'biometric-attendance:3', 'evt-2');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->orderBy('check_in')->get();

    expect($rows)->toHaveCount(2)
        ->and($rows[0]->work_date->format('Y-m-d'))->toBe('2026-10-01')
        ->and($rows[0]->check_out)->toBeNull()
        ->and($rows[1]->work_date->format('Y-m-d'))->toBe('2026-10-01')
        ->and($rows[1]->getRawOriginal('check_in'))->toBe('2026-10-01 16:40:00');
});

it('keeps a human checkout on an open device row when no newer punch closes it', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 08:00:00', 0, 'biometric-attendance:3', 'evt-1');

    Artisan::call('attendance:process-events');

    Attendance::where('employee_id', $employee->id)->firstOrFail()->update(['check_out' => '2026-10-01 17:00:00']);

    EventGateway::push($employee->id, '2026-10-01 08:00:30', 0, 'biometric-attendance:3', 'evt-2');

    Artisan::call('attendance:process-events');

    $rows = Attendance::where('employee_id', $employee->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->getRawOriginal('check_out'))->toBe('2026-10-01 17:00:00');
});

it('never touches manual rows of the same employee', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    $manual = Attendance::create([
        'employee_id' => $employee->id,
        'work_date'   => '2026-10-01',
        'check_in'    => '2026-10-01 08:00:00',
        'check_out'   => '2026-10-01 17:00:00',
        'source'      => 'manual',
    ]);

    EventGateway::push($employee->id, '2026-10-01 08:05:00', 0, 'biometric-attendance:3', 'evt-1');
    EventGateway::push($employee->id, '2026-10-01 17:02:00', 0, 'biometric-attendance:3', 'evt-2');

    Artisan::call('attendance:process-events');

    $manual->refresh();

    expect($manual->getRawOriginal('check_in'))->toBe('2026-10-01 08:00:00')
        ->and($manual->getRawOriginal('check_out'))->toBe('2026-10-01 17:00:00')
        ->and(Attendance::where('employee_id', $employee->id)->where('source', 'biometric-attendance:3')->count())->toBe(1);
});

it('skips the run while another pairing run holds the lock', function () {
    $employee = Employee::factory()->create(['time_zone' => 'UTC']);

    EventGateway::push($employee->id, '2026-10-01 08:00:00', 0, 'biometric-attendance:3', 'evt-1');

    $lock = Cache::lock(ProcessEventsCommand::LOCK_NAME, 600);
    $lock->get();

    Artisan::call('attendance:process-events');

    expect(Attendance::where('employee_id', $employee->id)->count())->toBe(0);

    $lock->release();

    Artisan::call('attendance:process-events');

    expect(Attendance::where('employee_id', $employee->id)->count())->toBe(1);
});
