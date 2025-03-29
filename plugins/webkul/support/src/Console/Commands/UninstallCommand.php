<?php

namespace Webkul\Support\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\Support\Package;

class UninstallCommand extends Command
{
    protected Package $package;

    public ?Closure $startWith = null;

    public ?Closure $endWith = null;

    public $hidden = true;

    protected bool $forceUninstall = false;

    public function __construct(Package $package)
    {
        $this->signature = $package->shortName().':uninstall {--force : Force the operation to run without confirmation}';

        $this->description = 'Uninstall '.$package->name;

        $this->package = $package;

        parent::__construct();
    }

    public function handle()
    {
        if (! $this->package->isInstalled()) {
            $this->error("Package {$this->package->shortName()} is not installed!");

            return;
        }

        if (! $this->package->getPlugin()->dependents->isEmpty()) {
            $this->error("Package {$this->package->shortName()} has dependents: <comment>".$this->package->getPlugin()->dependents->pluck('name')->implode(', ').'</comment>. Please uninstall dependents first!');

            return;
        }

        if ($this->startWith) {
            ($this->startWith)($this);
        }

        $this->forceUninstall = $this->option('force');

        if (! $this->forceUninstall && ! $this->confirm('Are you sure you want to uninstall this package? This action cannot be undone!')) {
            $this->info('Uninstallation cancelled.');

            return;
        }

        $this->dropTables();

        $this->package->delete();

        $this->info("🗑️ Package <comment>{$this->package->shortName()}</comment> has been uninstalled!");

        if ($this->endWith) {
            ($this->endWith)($this);
        }
    }

    protected function dropTables(): void
    {
        $this->info("⚙️ Dropping database tables for <comment>{$this->package->shortName()}</comment>...");

        $migrations = array_reverse($this->package->migrationFileNames);

        foreach ($migrations as $migration) {
            $fullPath = $this->package->basePath("../database/migrations/{$migration}.php");
            
            $migrationPath = Str::after($fullPath, base_path() . DIRECTORY_SEPARATOR);

            if (file_exists($migrationPath)) {
                require_once $migrationPath;

                $migrationInstance = require $migrationPath;

                if (is_object($migrationInstance) && method_exists($migrationInstance, 'down')) {
                    $this->info("🗑️ Rolling back migration: {$migration}");

                    $migrationInstance->down();
                }
            }

            DB::table('migrations')->where('migration', $migration)->delete();
        }

        foreach ($this->package->settingFileNames as $setting) {
            $fullPath = $this->package->basePath("../database/settings/{$setting}.php");
            
            $migrationPath = Str::after($fullPath, base_path() . DIRECTORY_SEPARATOR);

            if (file_exists($migrationPath)) {
                require_once $migrationPath;

                $migrationInstance = require $migrationPath;

                if (is_object($migrationInstance) && method_exists($migrationInstance, 'down')) {
                    $this->info("🗑️ Rolling back setting migration: {$migration}");

                    $migrationInstance->down();
                }
            }

            DB::table('migrations')->where('migration', $setting)->delete();
        }

        $this->info('✅ Database tables dropped successfully.');

        $this->newLine();
    }

    public function startWith($callable): self
    {
        $this->startWith = $callable;

        return $this;
    }

    public function endWith($callable): self
    {
        $this->endWith = $callable;

        return $this;
    }
}
