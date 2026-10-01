<?php

namespace Webkul\Attendance;

use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Policies\AttendancePolicy;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class AttendanceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'attendance';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasMigrations([
                '2026_09_24_000001_create_attendance_attendances_table',
                '2026_09_30_000001_add_source_label_to_attendance_attendances_table',
            ])
            ->hasDependencies([
                'employees',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('attendance');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(AttendancePlugin::make());
        });
    }

    public function packageBooted(): void
    {
        Gate::policy(Attendance::class, AttendancePolicy::class);
    }
}
