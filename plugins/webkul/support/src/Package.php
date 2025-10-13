<?php

namespace Webkul\Support;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package as BasePackage;
use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Models\Plugin;

class Package extends BasePackage
{
    public static $plugins = [];

    public ?Plugin $plugin = null;

    public bool $isCore = false;

    public bool $runsSettings = false;

    public array $settingFileNames = [];

    public array $dependencies = [];

    public bool $runsSeeders = false;

    public array $seederClasses = [];

    public function hasInstallCommand($callable): static
    {
        $installCommand = new InstallCommand($this);

        $callable($installCommand);

        $this->consoleCommands[] = $installCommand;

        return $this;
    }

    public function hasUninstallCommand($callable): static
    {
        $uninstallCommand = new UninstallCommand($this);

        $callable($uninstallCommand);

        $this->consoleCommands[] = $uninstallCommand;

        return $this;
    }

    public function isCore(bool $isCore = true): static
    {
        $this->isCore = $isCore;

        return $this;
    }

    public function runsSettings(bool $runsSettings = true): static
    {
        $this->runsSettings = $runsSettings;

        return $this;
    }

    public function hasSetting(string $settingFileName): static
    {
        $this->settingFileNames[] = $settingFileName;

        return $this;
    }

    public function hasSettings(...$settingFileNames): static
    {
        $this->settingFileNames = array_merge(
            $this->settingFileNames,
            collect($settingFileNames)->flatten()->toArray()
        );

        return $this;
    }

    public function runsSeeders(bool $runsSeeders = true): static
    {
        $this->runsSeeders = $runsSeeders;

        return $this;
    }

    public function hasSeeder(string $seederClass): static
    {
        $this->seederClasses[] = $seederClass;

        return $this;
    }

    public function hasSeeders(...$seederClasses): static
    {
        $this->seederClasses = array_merge(
            $this->seederClasses,
            collect($seederClasses)->flatten()->toArray()
        );

        return $this;
    }

    public function hasDependency(string $dependency): static
    {
        $this->dependencies[] = $dependency;

        return $this;
    }

    public function hasDependencies(...$dependencies): static
    {
        $this->dependencies = array_merge(
            $this->dependencies,
            collect($dependencies)->flatten()->toArray()
        );

        return $this;
    }

    public function delete(): void
    {
        Plugin::where('name', $this->name)->delete();

        unset(static::$plugins[$this->name]);

        $this->plugin = null;
    }

    public function updateOrCreate(): Plugin
    {
        $this->plugin = Plugin::updateOrCreate([
            'name' => $this->name,
        ], [
            'author'         => $this->author ?? null,
            'summary'        => $this->summary ?? null,
            'description'    => $this->description ?? null,
            'latest_version' => $this->version ?? null,
            'license'        => $this->license ?? null,
            'sort'           => $this->sort ?? null,
            'is_active'      => true,
            'is_installed'   => true,
        ]);

        static::$plugins[$this->name] = $this->plugin;

        return $this->plugin;
    }

    public function getPlugin(): ?Plugin
    {
        if ($this->plugin) {
            return $this->plugin;
        }

        return $this->plugin = static::getPackagePlugin($this->name);
    }

    public function isInstalled(): bool
    {
        return static::isPluginInstalled($this->name);
    }

    public static function getPackagePlugin(string $name): ?Plugin
    {
        if (count(static::$plugins) == 0) {
            if (Schema::hasTable('plugins') === false) {
                return null;
            }
            
            static::$plugins = Plugin::all()->keyBy('name');
        }

        if (isset(static::$plugins[$name])) {
            return static::$plugins[$name];
        }

        return static::$plugins[$name] ??= Plugin::where('name', $name)->first();
    }

    public static function isPluginInstalled(string $name): bool
    {
        try {
            if (count(static::$plugins) == 0) {
                DB::connection()->getPdo();

                if (Schema::hasTable('plugins') === false) {
                    return false;
                }

                static::$plugins = Plugin::all()->keyBy('name');
            }

            if (isset(static::$plugins[$name]) && static::$plugins[$name]->is_installed) {
                return true;
            }

            return false;
        } catch (Exception) {
            return false;
        }
    }
}
