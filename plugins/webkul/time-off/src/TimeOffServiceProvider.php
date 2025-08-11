<?php

namespace Webkul\TimeOff;

use Webkul\Employee\Models\Employee;
use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;
use Webkul\TimeOff\Models\Leave;

class TimeOffServiceProvider extends PackageServiceProvider
{
    public static string $name = 'time-off';

    public static string $viewNamespace = 'time-off';

    public function boot()
    {
        $this->handleDynamicRelations();

        $this->app->bind(
            \Webkul\Employee\Filament\Resources\EmployeeResource::class,
            \Webkul\TimeOff\Filament\Resources\EmployeeResource::class
        );

        parent::boot();
    }

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasMigrations([
                '2025_01_17_080711_create_time_off_leave_types_table',
                '2025_01_17_080712_create_time_off_leaves_table',
                '2025_01_20_080058_create_time_off_user_leave_types_table',
                '2025_01_20_130725_create_time_off_leave_mandatory_days_table',
                '2025_01_21_073921_create_time_off_leave_accrual_plans_table',
                '2025_01_21_085833_create_time_off_leave_accrual_levels_table',
                '2025_01_22_101656_create_time_off_leave_allocations_table',
            ])
            ->hasDependencies([
                'employees',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {});
    }

    protected function handleDynamicRelations(): void
    {
        Employee::resolveRelationUsing('leaves', function (Employee $employee) {
            return $employee->hasMany(Leave::class, 'employee_id');
        });
    }
}
