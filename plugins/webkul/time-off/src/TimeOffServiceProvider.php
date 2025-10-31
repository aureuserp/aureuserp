<?php

namespace Webkul\TimeOff;

use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class TimeOffServiceProvider extends PackageServiceProvider
{
    public static string $name = 'time-off';

    public static string $viewNamespace = 'time-off';

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
                '2025_08_13_120000_alter_private_name_column_in_time_off_leaves_table',
            ])
            ->hasDependencies([
                'employees',
            ])
            ->runsMigrations()
            ->hasSeeder('Webkul\\TimeOff\\Database\\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('time-offs');
    }
}
