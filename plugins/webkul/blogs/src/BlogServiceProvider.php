<?php

namespace Webkul\Blog;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class BlogServiceProvider extends PackageServiceProvider
{
    public static string $name = 'blogs';

    public static string $viewNamespace = 'blogs';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2025_03_06_093011_create_blogs_categories_table',
                '2025_03_06_094011_create_blogs_posts_table',
                '2025_03_07_065635_create_blogs_tags_table',
                '2025_03_07_065715_create_blogs_post_tags_table',
                '2025_09_03_070414_alter_blogs_posts_table',
            ])
            ->runsMigrations()
            ->hasSettings([
            ])
            ->runsSettings()
            ->hasDependencies([
                'website',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('blog');
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('blogs', __DIR__.'/../resources/dist/blogs.css'),
        ], 'blogs');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(BlogPlugin::make());
        });
    }
}
