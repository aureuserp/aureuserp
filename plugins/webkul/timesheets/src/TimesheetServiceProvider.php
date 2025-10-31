<?php

namespace Webkul\Timesheet;

use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class TimesheetServiceProvider extends PackageServiceProvider
{
    public static string $name = 'timesheets';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasDependencies([
                'projects',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('timesheet');
    }

    public function packageBooted(): void
    {
        //
    }
}
