<?php

namespace Webkul\PluginManager\Filament\Resources;

use Exception;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Filament\Resources\PluginResource\Pages\ListPlugins;
use Webkul\PluginManager\Filament\Resources\PluginResource\Schemas\PluginInfolist;
use Webkul\PluginManager\Filament\Resources\PluginResource\Tables\PluginsTable;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Enums\NavigationGroup;

class PluginResource extends Resource
{
    protected static ?string $model = Plugin::class;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Plugin;
    }

    public static function getModelLabel(): string
    {
        return __('plugin-manager::filament/resources/plugin.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('plugin-manager::filament/resources/plugin.title');
    }

    public static function table(Table $table): Table
    {
        return PluginsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PluginInfolist::configure($schema);
    }

    public static function repeatableEntry(string $type, string $color, string $key): RepeatableEntry
    {
        return RepeatableEntry::make($type)
            ->label(__('plugin-manager::filament/resources/plugin.infolist.'.$key.'.title'))
            ->state(function ($record) use ($type) {
                return collect($record->{'get'.ucfirst($type).'FromConfig'}())->map(fn ($dep) => [
                    'name'         => $dep,
                    'is_installed' => Package::isPluginInstalled($dep),
                ]);
            })
            ->schema([
                TextEntry::make('name')
                    ->label(__('plugin-manager::filament/resources/plugin.infolist.'.$key.'.name'))
                    ->formatStateUsing(fn ($state) => self::localize('names', $state, ucfirst($state)))
                    ->badge()
                    ->color($color),

                IconEntry::make('is_installed')
                    ->label(__('plugin-manager::filament/resources/plugin.infolist.'.$key.'.is_installed'))
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->columns(2)
            ->placeholder(__('plugin-manager::filament/resources/plugin.infolist.'.$key.'.placeholder'));
    }

    public static function uninstallPlugin($record)
    {
        $errors = [];

        $dependents = $record->getDependentsFromConfig();

        $installedDependents = collect($dependents)
            ->filter(fn ($dependent) => Package::isPluginInstalled($dependent))
            ->values();

        if ($installedDependents->isNotEmpty()) {
            Notification::make()
                ->title(__('plugin-manager::filament/resources/plugin.notifications.uninstalled-blocked.title'))
                ->body(__('plugin-manager::filament/resources/plugin.notifications.uninstalled-blocked.body', [
                    'name'       => $record->name,
                    'dependents' => $installedDependents->map(fn ($dependent) => ucfirst($dependent))->implode(', '),
                ]))
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        collect($dependents)
            ->push($record->name)
            ->each(function ($pluginName) use (&$errors) {
                $plugin = Plugin::where('name', $pluginName)->first();

                if (! $plugin?->is_installed) {
                    return;
                }

                try {
                    if (! $plugin->package) {
                        throw new Exception("Package for '{$pluginName}' not found.");
                    }

                    $uninstallCommand = static::resolveUninstallCommand($plugin->package);

                    if ($uninstallCommand?->startWith) {
                        ($uninstallCommand->startWith)($uninstallCommand);
                    }

                    collect(array_reverse($plugin->package->migrationFileNames))
                        ->each(function ($migration) use ($plugin) {
                            $fullPath = $plugin->package->basePath("database/migrations/{$migration}.php");

                            static::downMigration($fullPath, $migration);
                        });

                    collect($plugin->package->settingFileNames)
                        ->each(function ($setting) use ($plugin) {
                            $fullPath = $plugin->package->basePath("database/settings/{$setting}.php");

                            static::downMigration($fullPath, $setting);
                        });

                    $plugin->update(['is_installed' => false, 'is_active' => false]);

                    if ($uninstallCommand?->endWith) {
                        ($uninstallCommand->endWith)($uninstallCommand);
                    }
                } catch (Throwable $e) {
                    $errors[] = "Failed to uninstall '{$pluginName}': ".$e->getMessage();
                }
            });

        Package::refreshPluginCaches();

        if (empty($errors)) {
            Notification::make()
                ->title(__('plugin-manager::filament/resources/plugin.notifications.uninstalled.title'))
                ->body(__('plugin-manager::filament/resources/plugin.notifications.uninstalled.body', ['name' => $record->name]))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('plugin-manager::filament/resources/plugin.notifications.uninstalled-failed.title'))
                ->body(implode(' ', $errors))
                ->danger()
                ->persistent()
                ->send();
        }
    }

    protected static function resolveUninstallCommand(Package $package): ?UninstallCommand
    {
        return collect($package->consoleCommands ?? [])
            ->first(fn ($command) => $command instanceof UninstallCommand);
    }

    protected static function downMigration(string $fullPath, string $migration): void
    {
        if (! file_exists($fullPath)) {
            return;
        }

        if (! DB::table('migrations')->where('migration', $migration)->exists()) {
            return;
        }

        require_once $fullPath;

        $migrationInstance = require $fullPath;

        if (is_object($migrationInstance) && method_exists($migrationInstance, 'down')) {
            $migrationInstance->down();

            DB::table('migrations')->where('migration', $migration)->delete();
        }
    }

    public static function localize(string $group, string $name, ?string $fallback = null): string
    {
        $key = "plugin-manager::filament/resources/plugin.{$group}.{$name}";

        return Lang::has($key) ? __($key) : ($fallback ?? $name);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlugins::route('/'),
        ];
    }

    public static function getPhpExecutablePath(): string
    {
        return Package::phpBinaryPath();
    }

    public static function buildTimeoutCommand(int $seconds, string $command): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return $command;
        }

        if (PHP_OS_FAMILY === 'Darwin') {
            $gtimeout = trim((string) shell_exec('which gtimeout 2>/dev/null'));

            if ($gtimeout !== '') {
                return "gtimeout {$seconds} {$command}";
            }

            return $command;
        }

        return "timeout {$seconds} {$command}";
    }
}
