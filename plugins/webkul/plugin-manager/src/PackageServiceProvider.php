<?php

namespace Webkul\PluginManager;

use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;
use Spatie\LaravelPackageTools\Package as BasePackage;
use Spatie\LaravelPackageTools\PackageServiceProvider as BasePackageServiceProvider;

abstract class PackageServiceProvider extends BasePackageServiceProvider
{
    protected BasePackage $package;

    abstract public function configureCustomPackage(Package $package): void;

    public function register()
    {
        $this->registeringPackage();

        $this->package = $this->newPackage();

        $this->package->setBasePath($this->getPackageBaseDir());

        $this->configureCustomPackage($this->package);

        if (empty($this->package->name)) {
            throw InvalidPackage::nameIsRequired();
        }

        foreach ($this->package->configFileNames as $configFileName) {
            $this->mergeConfigFrom($this->package->basePath("/../config/{$configFileName}.php"), $configFileName);
        }

        $this->mergeShieldConfig();

        $this->packageRegistered();

        return $this;
    }

    protected function mergeShieldConfig(): void
    {
        $shieldConfigPath = $this->package->basePath('/../config/filament-shield.php');

        if (! file_exists($shieldConfigPath)) {
            return;
        }

        $shieldConfig = include $shieldConfigPath;
        $config = $this->app->make('config');

        if (isset($shieldConfig['resources']['manage'])) {
            $existingManage = $config->get('filament-shield.resources.manage', []);
            $config->set('filament-shield.resources.manage', array_merge($existingManage, $shieldConfig['resources']['manage']));
        }

        if (isset($shieldConfig['resources']['exclude'])) {
            $existingExclude = $config->get('filament-shield.resources.exclude', []);
            $config->set('filament-shield.resources.exclude', array_merge($existingExclude, $shieldConfig['resources']['exclude']));
        }

        if (isset($shieldConfig['pages']['exclude'])) {
            $existingPagesExclude = $config->get('filament-shield.pages.exclude', []);
            $config->set('filament-shield.pages.exclude', array_merge($existingPagesExclude, $shieldConfig['pages']['exclude']));
        }

        if (isset($shieldConfig['widgets']['exclude'])) {
            $existingWidgetsExclude = $config->get('filament-shield.widgets.exclude', []);
            $config->set('filament-shield.widgets.exclude', array_merge($existingWidgetsExclude, $shieldConfig['widgets']['exclude']));
        }
    }

    public function newPackage(): Package
    {
        return new Package;
    }

    public function configurePackage(BasePackage $package): void {}

    public function boot()
    {
        $this->bootingPackage();

        if ($this->package->hasTranslations) {
            $langPath = 'vendor/'.$this->package->shortName();

            $langPath = (function_exists('lang_path'))
                ? lang_path($langPath)
                : resource_path('lang/'.$langPath);
        }

        if ($this->app->runningInConsole()) {
            foreach ($this->package->configFileNames as $configFileName) {
                $this->publishes([
                    $this->package->basePath("/../config/{$configFileName}.php") => config_path("{$configFileName}.php"),
                ], "{$this->package->shortName()}-config");
            }

            if ($this->package->hasViews) {
                $this->publishes([
                    $this->package->basePath('/../resources/views') => base_path("resources/views/vendor/{$this->packageView($this->package->viewNamespace)}"),
                ], "{$this->packageView($this->package->viewNamespace)}-views");
            }

            if ($this->package->hasInertiaComponents) {
                $packageDirectoryName = Str::of($this->packageView($this->package->viewNamespace))->studly()->remove('-')->value();

                $this->publishes([
                    $this->package->basePath('/../resources/js/Pages') => base_path("resources/js/Pages/{$packageDirectoryName}"),
                ], "{$this->packageView($this->package->viewNamespace)}-inertia-components");
            }

            $now = Carbon::now();
            foreach ($this->package->migrationFileNames as $migrationFileName) {
                $filePath = $this->package->basePath("/../database/migrations/{$migrationFileName}.php");
                if (! file_exists($filePath)) {
                    // Support for the .stub file extension
                    $filePath .= '.stub';
                }

                $this->publishes([
                    $filePath => $this->generateMigrationName(
                        $migrationFileName,
                        $now->addSecond()
                    ),
                ], "{$this->package->shortName()}-migrations");

                if ($this->package->runsMigrations) {
                    if ($this->package->isCore) {
                        $this->loadMigrationsFrom($filePath);
                    } elseif ($this->package->isInstalled()) {
                        $this->loadMigrationsFrom($filePath);
                    }
                }
            }

            foreach ($this->package->settingFileNames as $settingFileName) {
                $filePath = $this->package->basePath("/../database/settings/{$settingFileName}.php");
                if (! file_exists($filePath)) {
                    // Support for the .stub file extension
                    $filePath .= '.stub';
                }

                $this->publishes([
                    $filePath => $this->generateSettingName(
                        $settingFileName,
                        $now->addSecond()
                    ),
                ], "{$this->package->shortName()}-settings");

                if ($this->package->runsSettings) {
                    if ($this->package->isCore) {
                        $this->loadMigrationsFrom($filePath);
                    } elseif ($this->package->isInstalled()) {
                        $this->loadMigrationsFrom($filePath);
                    }
                }
            }
        }

        if (! empty($this->package->commands)) {
            $this->commands($this->package->commands);
        }

        if (! empty($this->package->consoleCommands) && $this->app->runningInConsole()) {
            $this->commands($this->package->consoleCommands);
        }

        if ($this->package->hasTranslations) {
            $this->loadTranslationsFrom(
                $this->package->basePath('/../resources/lang/'),
                $this->package->shortName()
            );

            $this->loadJsonTranslationsFrom($this->package->basePath('/../resources/lang/'));

            $this->loadJsonTranslationsFrom($langPath);
        }

        if ($this->package->hasViews) {
            $this->loadViewsFrom($this->package->basePath('/../resources/views'), $this->package->viewNamespace());
        }

        foreach ($this->package->viewComponents as $componentClass => $prefix) {
            $this->loadViewComponentsAs($prefix, [$componentClass]);
        }

        if (count($this->package->viewComponents)) {
            $this->publishes([
                $this->package->basePath('/Components') => base_path("app/View/Components/vendor/{$this->package->shortName()}"),
            ], "{$this->package->name}-components");
        }

        if ($this->package->publishableProviderName) {
            $this->publishes([
                $this->package->basePath("/../resources/stubs/{$this->package->publishableProviderName}.php.stub") => base_path("app/Providers/{$this->package->publishableProviderName}.php"),
            ], "{$this->package->shortName()}-provider");
        }

        foreach ($this->package->routeFileNames as $routeFileName) {
            $this->loadRoutesFrom("{$this->package->basePath('/../routes/')}{$routeFileName}.php");
        }

        foreach ($this->package->sharedViewData as $name => $value) {
            View::share($name, $value);
        }

        foreach ($this->package->viewComposers as $viewName => $viewComposer) {
            View::composer($viewName, $viewComposer);
        }

        $this->packageBooted();

        return $this;
    }

    public static function generateSettingName(string $settingFileName, Carbon $now): string
    {
        $settingsPath = 'settings/'.dirname($settingFileName).'/';
        $settingFileName = basename($settingFileName);

        $len = strlen($settingFileName) + 4;

        if (Str::contains($settingFileName, '/')) {
            $settingsPath .= Str::of($settingFileName)->beforeLast('/')->finish('/');

            $settingFileName = Str::of($settingFileName)->afterLast('/');
        }

        foreach (glob(database_path("{$settingsPath}*.php")) as $filename) {
            if ((substr($filename, -$len) === $settingFileName.'.php')) {
                return $filename;
            }
        }

        return database_path($settingsPath.$now->format('Y_m_d_His').'_'.Str::of($settingFileName)->snake()->finish('.php'));
    }
}
