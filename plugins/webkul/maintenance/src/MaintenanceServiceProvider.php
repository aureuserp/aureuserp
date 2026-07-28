<?php

namespace Webkul\Maintenance;

use Filament\Panel;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Maintenance\Models\MaintenanceRequest;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class MaintenanceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'maintenance';

    public static string $viewNamespace = 'maintenance';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_05_18_000001_create_maintenance_equipment_categories_table',
                '2026_05_18_000002_create_maintenance_stages_table',
                '2026_05_18_000003_create_maintenance_teams_table',
                '2026_05_18_000004_create_maintenance_equipments_table',
                '2026_05_18_000005_create_maintenance_requests_table',
                '2026_05_18_000006_create_maintenance_team_users_table',
            ])
            ->runsMigrations()
            ->hasSeeder('Webkul\\Maintenance\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                $command->endWith(function () {
                    ChatterCleanupService::purgeForModels([MaintenanceRequest::class]);
                });
            })
            ->icon('maintenance');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(MaintenancePlugin::make());
        });
    }
}
