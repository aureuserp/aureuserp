<?php

namespace Webkul\PluginManager\Filament\Resources;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DBSchema;
use RuntimeException;
use Throwable;
use Webkul\PluginManager\Filament\Resources\PluginResource\Pages\ListPlugins;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

class PluginResource extends Resource
{
    protected static ?string $model = Plugin::class;

    public static function getNavigationGroup(): string
    {
        return __('plugin-manager::filament/resources/plugin.navigation.group');
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
        return $table
            ->columns([
                Split::make([
                    IconColumn::make('package_icon')
                        ->label('')
                        ->state(true)
                        ->icon('heroicon-o-puzzle-piece')
                        ->size(IconSize::TwoExtraLarge)
                        ->color('primary')
                        ->visible(fn ($record) => ! $record?->package?->icon)
                        ->grow(false),

                    ImageColumn::make('package_image')
                        ->label('')
                        ->getStateUsing(fn ($record) => $record?->package?->icon
                            ? asset("svg/{$record->package->icon}.svg")
                            : null)
                        ->imageSize(100)
                        ->visible(fn ($record) => $record?->package?->icon)
                        ->grow(false),

                    Stack::make([
                        Split::make([
                            TextColumn::make('name')
                                ->weight('semibold')
                                ->searchable()
                                ->size(TextSize::Large)
                                ->formatStateUsing(fn (string $state) => ucfirst($state))
                                ->grow(false),

                            TextColumn::make('latest_version')
                                ->label(__('plugin-manager::filament/resources/plugin.table.version'))
                                ->default('1.0.0')
                                ->badge()
                                ->color('info'),
                        ]),

                        TextColumn::make('summary')
                            ->color('gray')
                            ->limit(80)
                            ->wrap(),

                        Split::make([
                            TextColumn::make('is_installed')
                                ->badge()
                                ->inline()
                                ->grow(false)
                                ->formatStateUsing(fn ($record) => $record->is_installed
                                    ? __('plugin-manager::filament/resources/plugin.status.installed')
                                    : __('plugin-manager::filament/resources/plugin.status.not_installed'))
                                ->color(fn ($record) => $record->is_installed ? 'success' : 'gray'),

                            TextColumn::make('dependencies_count')
                                ->label(__('plugin-manager::filament/resources/plugin.table.dependencies'))
                                ->state(fn ($record) => count($record->getDependenciesFromConfig()))
                                ->badge()
                                ->color('warning')
                                ->suffix(__('plugin-manager::filament/resources/plugin.table.dependencies_suffix'))
                                ->default(0),
                        ]),
                    ])->space(1),
                ]),
            ])
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'lg'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->icon('heroicon-o-eye'),

                    Action::make('install')
                        ->label(__('plugin-manager::filament/resources/plugin.actions.install.title'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn ($record) => ! $record->is_installed)
                        ->requiresConfirmation()
                        ->modalHeading(fn ($record) => __('plugin-manager::filament/resources/plugin.actions.install.heading', ['name' => $record->name]))
                        ->modalDescription(fn ($record) => __('plugin-manager::filament/resources/plugin.actions.install.description', ['name' => $record->name]))
                        ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.install.submit'))
                        ->action(function ($record) {
                            DB::beginTransaction();

                            try {
                                $phpPath = self::getPhpExecutablePath();

                                $php = escapeshellarg($phpPath);

                                $artisan = escapeshellarg(base_path('artisan'));

                                $commandName = escapeshellarg("{$record->name}:install");

                                $cmd = "timeout 300 $php $artisan $commandName 2>&1";

                                $output = [];

                                $exitCode = 0;

                                exec($cmd, $output, $exitCode);

                                if ($exitCode === 124) {
                                    throw new RuntimeException('Installation timed out after 5 minutes.');
                                }

                                if ($exitCode !== 0) {
                                    $errorOutput = implode(PHP_EOL, array_slice($output, -10));

                                    throw new RuntimeException(
                                        "Installation failed with exit code {$exitCode}.".
                                            ($errorOutput ? " Last output: {$errorOutput}" : '')
                                    );
                                }

                                $record->update([
                                    'is_installed' => true,
                                    'is_active'    => true,
                                ]);

                                DB::commit();

                                Notification::make()
                                    ->title(__('plugin-manager::filament/resources/plugin.notifications.installed.title'))
                                    ->body(__('plugin-manager::filament/resources/plugin.notifications.installed.body', ['name' => $record->name]))
                                    ->success()
                                    ->send();
                            } catch (Throwable $e) {
                                DB::rollBack();

                                logger()->error('Plugin installation failed', [
                                    'plugin' => $record->name,
                                    'error'  => $e->getMessage(),
                                    'trace'  => $e->getTraceAsString(),
                                ]);

                                Notification::make()
                                    ->title(__('plugin-manager::filament/resources/plugin.notifications.installed-failed.title'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        })
                        ->after(fn () => redirect(self::getUrl('index'))),

                    Action::make('uninstall')
                        ->label(__('plugin-manager::filament/resources/plugin.actions.uninstall.title'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->modalWidth(Width::ExtraLarge)
                        ->visible(fn ($record) => $record->is_installed)
                        ->modalHeading(__('plugin-manager::filament/resources/plugin.actions.uninstall.heading'))
                        ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.uninstall.submit'))
                        ->modalContent(function ($record) {
                            $dependents = $record->getDependentsFromConfig();

                            $packages = collect([$record->name => $record->package])
                                ->merge(
                                    $dependents
                                        ? collect($dependents)->mapWithKeys(fn ($dep) => [$dep => Plugin::where('name', $dep)->first()?->package])
                                        : []
                                );

                            $tables = $packages
                                ->flatMap(fn ($package) => collect($package?->migrationFileNames ?? []))
                                ->map(function ($migrationFile) {
                                    if (preg_match('/create_(.*?)_table/', $migrationFile, $matches)) {
                                        $table = $matches[1];

                                        return DBSchema::hasTable($table)
                                            ? ['table' => $table, 'count' => DB::table($table)->count()]
                                            : null;
                                    }

                                    return null;
                                })
                                ->filter()
                                ->filter(fn ($item) => $item['count'] > 0)
                                ->unique('table')
                                ->values();

                            return view('plugin-manager::uninstall-modal', compact('record', 'dependents', 'tables'));
                        })
                        ->action(fn ($record) => self::uninstallPlugin($record))
                        ->after(fn () => redirect(self::getUrl('index'))),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->recordActionsAlignment('end')
            ->defaultSort('sort', 'asc')
            ->paginated([16, 24, 32]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('plugin-manager::filament/resources/plugin.infolist.section.plugin'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.name'))
                                ->formatStateUsing(fn ($state) => ucfirst($state))
                                ->weight('bold')
                                ->size('lg'),

                            TextEntry::make('latest_version')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.version'))
                                ->badge()
                                ->color('info'),
                        ]),

                    Grid::make(2)
                        ->schema([
                            IconEntry::make('is_installed')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.is_installed'))
                                ->boolean()
                                ->trueIcon('heroicon-s-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('gray'),

                            TextEntry::make('author')
                                ->label('Author')
                                ->badge(),
                        ]),

                    TextEntry::make('license')
                        ->label(__('plugin-manager::filament/resources/plugin.infolist.license'))
                        ->default('MIT')
                        ->badge()
                        ->color('success'),

                    TextEntry::make('summary')
                        ->label(__('plugin-manager::filament/resources/plugin.infolist.summary'))
                        ->columnSpanFull(),
                ]),

            Group::make([
                Section::make(__('plugin-manager::filament/resources/plugin.infolist.section.dependencies'))
                    ->schema([
                        self::repeatableEntry('dependencies', 'warning', 'dependencies-repeater'),
                        self::repeatableEntry('dependents', 'info', 'dependents-repeater'),
                    ]),
            ]),
        ]);
    }

    protected static function repeatableEntry(string $type, string $color, string $key): RepeatableEntry
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
                    ->formatStateUsing(fn ($state) => ucfirst($state))
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

    protected static function uninstallPlugin($record)
    {
        $errors = [];

        $dependents = $record->getDependentsFromConfig();

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
                } catch (Throwable $e) {
                    $errors[] = "Failed to uninstall '{$pluginName}': ".$e->getMessage();
                }
            });

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

    protected static function downMigration(string $fullPath, string $migration): void
    {
        if (! file_exists($fullPath)) {
            return;
        }

        require_once $fullPath;

        $migrationInstance = require $fullPath;

        if (is_object($migrationInstance) && method_exists($migrationInstance, 'down')) {
            $migrationInstance->down();

            DB::table('migrations')->where('migration', $migration)->delete();
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlugins::route('/'),
        ];
    }

    protected static function getPhpExecutablePath(): string
    {
        $phpPath = trim(shell_exec('which php 2>/dev/null') ?: '');

        if (
            $phpPath
            && file_exists($phpPath)
        ) {
            return $phpPath;
        }

        $phpPath = PHP_BINARY;

        if (strpos($phpPath, 'fpm') !== false) {
            $phpPath = str_replace('fpm', '', $phpPath);
        }

        if (file_exists($phpPath)) {
            return $phpPath;
        }

        $commonPaths = [
            '/usr/local/bin/php',
            '/usr/bin/php',
            '/opt/homebrew/bin/php',
            '/Users/'.get_current_user().'/Library/Application Support/Herd/bin/php',
        ];

        foreach ($commonPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return 'php';
    }
}
