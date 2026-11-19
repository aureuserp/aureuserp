<?php

namespace Webkul\Project;

use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\View\View;
use Livewire\Livewire;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Project\Enums\TaskViewLayout;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ListTasks;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Support\Support\VersionedCss;

class ProjectServiceProvider extends PackageServiceProvider
{
    public static string $name = 'projects';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasRoute('api')
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2024_12_12_074920_create_projects_project_stages_table',
                '2024_12_12_074929_create_projects_projects_table',
                '2024_12_12_074930_create_projects_milestones_table',
                '2024_12_12_100227_create_projects_user_project_favorites_table',
                '2024_12_12_100230_create_projects_tags_table',
                '2024_12_12_100232_create_projects_project_tag_table',
                '2024_12_12_101340_create_projects_task_stages_table',
                '2024_12_12_101344_create_projects_tasks_table',
                '2024_12_12_101350_create_projects_task_users_table',
                '2024_12_12_101352_create_projects_task_tag_table',
                '2024_12_18_145142_add_columns_to_analytic_records_table',
                '2025_09_24_062711_remove_tags_column_from_projects_tasks_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2024_12_16_094021_create_project_task_settings',
                '2024_12_16_094021_create_project_time_settings',
            ])
            ->runsSettings()
            ->hasSeeder('Webkul\\Project\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                $command->endWith(function () {
                    ChatterCleanupService::purgeForModels([Project::class, Task::class]);
                });
            })
            ->icon('projects');
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            VersionedCss::make('projects', __DIR__.'/../resources/dist/projects.css'),
        ], 'projects');

        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER,
            fn (): string|View => method_exists($page = Livewire::current(), 'getTaskViewLayouts')
                ? view('projects::filament.view-layout-switcher', [
                    'layouts'      => $page->getTaskViewLayouts(),
                    'activeLayout' => $page->getTaskViewLayout(),
                ])
                : '',
            scopes: [ManageTasks::class, ListTasks::class],
        );

        FilamentView::registerRenderHook(
            TablesRenderHook::CONTENT_BEFORE,
            fn (): string|View => method_exists($page = Livewire::current(), 'getTaskViewLayouts')
                ? view('projects::filament.view-layout-shimmer', [
                    'layouts'      => $page->getTaskViewLayouts(),
                    'activeLayout' => $page->getTaskViewLayout(),
                ])
                : '',
            scopes: [ManageTasks::class, ListTasks::class],
        );

        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_START,
            fn (): string|View => method_exists($page = Livewire::current(), 'getTaskViewLayouts') && $page->isTaskViewLayout(TaskViewLayout::GRAPH)
                ? view('projects::filament.graph-controls', [
                    'graphType'    => $page->graphType,
                    'graphStacked' => $page->graphStacked,
                    'graphSort'    => $page->graphSort,
                ])
                : '',
            scopes: [ManageTasks::class, ListTasks::class],
        );
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(ProjectPlugin::make());
        });
    }
}
