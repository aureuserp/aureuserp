<?php

namespace Webkul\Support\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Support\Models\Plugin;
use Webkul\Support\Package;

class InstallCommand extends Command
{
    protected Package $package;

    public ?Closure $startWith = null;

    protected array $publishes = [];

    protected bool $askToInstallDependencies = false;

    protected bool $askToRunMigrations = false;

    protected bool $askToRunSeeders = false;

    protected bool $installDependencies = false;

    protected bool $runsMigrations = false;

    protected bool $runsSeeders = false;

    protected bool $copyServiceProviderInApp = false;

    protected ?string $starRepo = null;

    public ?Closure $endWith = null;

    public $hidden = true;

    public function __construct(Package $package)
    {
        $this->signature = $package->shortName().':install';

        $this->description = 'Install '.$package->name;

        $this->package = $package;

        parent::__construct();
    }

    public function handle()
    {
        if ($this->startWith) {
            ($this->startWith)($this);
        }

        if ($this->askToInstallDependencies) {
            $choice = $this->choice(
                "This package <comment>{$this->package->shortName()}</comment> has dependencies. What would you like to do?",
                ['Install', 'Skip', 'Show Dependencies'],
                0
            );

            if ($choice === 'Install') {
                $this->info("🚀 Installing <comment>{$this->package->shortName()}</comment> dependencies...");

                $this->newLine();

                foreach ($this->package->dependencies as $dependency) {
                    $this->comment('Installing <info>'.$dependency.'</info>...');

                    $this->newLine();

                    $this->call($dependency.':install');
                }

                $this->newLine();
            } elseif ($choice === 'Show Dependencies') {
                $this->info('This package requires the following dependencies:');

                foreach ($this->package->dependencies as $dependency) {
                    $this->line('- <info>'.$dependency.'</info>');
                }

                $this->newLine();

                return $this->handle();
            } else {
                $this->error('Please install the dependencies first.');

                return;
            }
        } elseif ($this->installDependencies) {
            $this->info("🚀 Installing <comment>{$this->package->shortName()}</comment> dependencies...");

            $this->newLine();

            foreach ($this->package->dependencies as $dependency) {
                $this->comment('Installing <info>'.$dependency.'</info>...');

                $this->newLine();

                $this->call($dependency.':install');
            }

            $this->newLine();
        }

        foreach ($this->publishes as $tag) {
            $name = str_replace('-', ' ', $tag);
            $this->comment("Publishing {$name}...");

            $this->callSilently('vendor:publish', [
                '--tag' => "{$this->package->shortName()}-{$tag}",
            ]);
        }

        if ($this->askToRunMigrations) {
            if ($this->confirm('Would you like to run the migrations now?')) {
                $this->runMigrations();
            }
        } elseif ($this->runsMigrations) {
            $this->runMigrations();
        }

        if ($this->askToRunSeeders) {
            if ($this->confirm('Would you like to seed the data now?')) {
                $this->runSeeders();
            }
        } elseif ($this->runsSeeders) {
            $this->runSeeders();
        }

        if ($this->copyServiceProviderInApp) {
            $this->comment('Publishing service provider...');

            $this->newLine();

            $this->copyServiceProviderInApp();
        }

        if ($this->starRepo) {
            if ($this->confirm('Would you like to star our repo on GitHub?')) {
                $repoUrl = "https://github.com/{$this->starRepo}";

                if (PHP_OS_FAMILY == 'Darwin') {
                    exec("open {$repoUrl}");
                }
                if (PHP_OS_FAMILY == 'Windows') {
                    exec("start {$repoUrl}");
                }
                if (PHP_OS_FAMILY == 'Linux') {
                    exec("xdg-open {$repoUrl}");
                }
            }
        }

        $package = $this->package->updateOrCreate();

        foreach ($this->package->dependencies as $dependencyName) {
            $dependency = Plugin::where('name', $dependencyName)->first();

            $package->dependencies()->syncWithoutDetaching($dependency);
        }

        if ($this->endWith) {
            ($this->endWith)($this);
        }

        $this->regenerateAdminPanelPermissions();

        $this->info("🎉 Package <comment>{$this->package->shortName()}</comment> has been installed!");
    }

    public function publish(string ...$tag): self
    {
        $this->publishes = array_merge($this->publishes, $tag);

        return $this;
    }

    public function publishConfigFile(): self
    {
        return $this->publish('config');
    }

    public function publishAssets(): self
    {
        return $this->publish('assets');
    }

    public function publishInertiaComponents(): self
    {
        return $this->publish('inertia-components');
    }

    public function publishMigrations(): self
    {
        return $this->publish('migrations');
    }

    public function askToInstallDependencies(): self
    {
        $this->askToInstallDependencies = true;

        return $this;
    }

    public function askToRunMigrations(): self
    {
        $this->askToRunMigrations = true;

        return $this;
    }

    public function askToRunSeeders(): self
    {
        $this->askToRunSeeders = true;

        return $this;
    }

    public function installDependencies(): self
    {
        $this->installDependencies = true;

        return $this;
    }

    public function runsMigrations(): self
    {
        $this->runsMigrations = true;

        return $this;
    }

    public function runsSeeders(): self
    {
        $this->runsSeeders = true;

        return $this;
    }

    public function runMigrations(): self
    {
        $migrationsToRun = collect([]);

        foreach ($this->package->migrationFileNames as $migration) {
            if ($this->hasMigrationAlreadyRun($migration)) {
                continue;
            }

            $fullPath = $this->package->basePath("../database/migrations/{$migration}.php");

            $path = Str::after($fullPath, base_path().DIRECTORY_SEPARATOR);

            $migrationsToRun[] = $path;
        }

        if (! $migrationsToRun->isEmpty()) {
            $this->info("⚙️ Running <comment>{$this->package->shortName()}</comment> database migrations...");

            foreach ($migrationsToRun as $migration) {
                $this->call('migrate', [
                    '--path' => $migration,
                ]);
            }

            $this->info("✅ Migrations <comment>{$this->package->shortName()}</comment> completed successfully.");

            $this->newLine();
        }

        $settingsToRun = collect([]);

        foreach ($this->package->settingFileNames as $setting) {
            if ($this->hasMigrationAlreadyRun($migration)) {
                continue;
            }

            $fullPath = $this->package->basePath("../database/settings/{$setting}.php");

            $path = Str::after($fullPath, base_path().DIRECTORY_SEPARATOR);

            $settingsToRun[] = $path;
        }

        if (! $settingsToRun->isEmpty()) {
            $this->info("⚙️ Running <comment>{$this->package->shortName()}</comment> settings database migrations...");

            foreach ($settingsToRun as $migration) {
                $this->call('migrate', [
                    '--path' => $migration,
                ]);
            }

            $this->info("✅ Settings migrations <comment>{$this->package->shortName()}</comment> completed successfully.");

            $this->newLine();
        }

        return $this;
    }

    public function runSeeders(): self
    {
        if ($this->package->isInstalled()) {
            $choice = $this->choice(
                "This package <comment>{$this->package->shortName()}</comment> is already installed. What would you like to do?",
                ['Reseed', 'Skip', 'Show Seeders'],
                1
            );

            if ($choice === 'Skip') {
                return $this;
            }

            if ($choice === 'Show Seeders') {
                $this->newLine();
                $this->info('This package includes the following seeders:');

                foreach ($this->package->seederClasses as $seeder) {
                    $this->line('- <info>'.$seeder.'</info>');
                }
                $this->newLine();

                return $this->runSeeders();
            }
        }

        $this->info("⚙️ Running <comment>{$this->package->shortName()}</comment> database seeders...");

        $this->newLine();

        foreach ($this->package->seederClasses as $seeder) {
            $this->call('db:seed', [
                '--class' => $seeder,
            ]);

            $this->newLine();
        }

        $this->info("✅ Seeders <comment>{$this->package->shortName()}</comment> completed successfully.");

        $this->newLine();

        return $this;
    }

    public function copyAndRegisterServiceProviderInApp(): self
    {
        $this->copyServiceProviderInApp = true;

        return $this;
    }

    public function askToStarRepoOnGitHub($vendorSlashRepoName): self
    {
        $this->starRepo = $vendorSlashRepoName;

        return $this;
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

    public function hasMigrationAlreadyRun($migrationName): bool
    {
        return DB::table('migrations')
            ->where('migration', $migrationName)
            ->exists();
    }

    protected function copyServiceProviderInApp(): self
    {
        $providerName = $this->package->publishableProviderName;

        if (! $providerName) {
            return $this;
        }

        $this->callSilent('vendor:publish', ['--tag' => $this->package->shortName().'-provider']);

        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        if (intval(app()->version()) < 11 || ! file_exists(base_path('bootstrap/providers.php'))) {
            $appConfig = file_get_contents(config_path('app.php'));
        } else {
            $appConfig = file_get_contents(base_path('bootstrap/providers.php'));
        }

        $class = '\\Providers\\'.Str::replace('/', '\\', $providerName).'::class';

        if (Str::contains($appConfig, $namespace.$class)) {
            return $this;
        }

        if (intval(app()->version()) < 11 || ! file_exists(base_path('bootstrap/providers.php'))) {
            file_put_contents(config_path('app.php'), str_replace(
                "{$namespace}\\Providers\\BroadcastServiceProvider::class,",
                "{$namespace}\\Providers\\BroadcastServiceProvider::class,".PHP_EOL."        {$namespace}{$class},",
                $appConfig
            ));
        } else {
            file_put_contents(base_path('bootstrap/providers.php'), str_replace(
                "{$namespace}\\Providers\\AppServiceProvider::class,",
                "{$namespace}\\Providers\\AppServiceProvider::class,".PHP_EOL."        {$namespace}{$class},",
                $appConfig
            ));
        }

        file_put_contents(app_path('Providers/'.$providerName.'.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/'.$providerName.'.php'))
        ));

        return $this;
    }

    protected function regenerateAdminPanelPermissions(): void
    {
        $this->info('⚙️ Refreshing access controls for the admin panel...');

        $php = escapeshellarg(PHP_BINARY);

        $artisan = escapeshellarg(base_path('artisan'));

        $cmd = "$php $artisan shield:generate --all --option=permissions --panel=admin";

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error('Failed to generate admin panel permissions. Command output: '.implode(PHP_EOL, $output));
        }

        $role = Role::first();

        $permissions = Permission::query()->pluck('id')->all();

        $role?->permissions()->sync($permissions);
    }
}
