<?php

namespace Webkul\PluginManager\Filament\Resources;

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
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Filament\Resources\PluginResource\Pages;
use Webkul\Support\Models\Plugin;
use Webkul\Support\Package;

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

    protected static ?int $navigationSort = 100;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                split::make([
                    ImageColumn::make('id')
                        ->label('')
                        ->getStateUsing(function ($record) {
                            $package = $record->getPackage();
                            $icon = $package ? $package->icon : '';

                            return $icon ? asset("svg/{$icon}.svg") : 'heroicon-o-puzzle-piece';
                        })
                        ->imageSize(100)
                        ->grow(false),
                    stack::make([
                        Split::make([
                            TextColumn::make('name')
                                ->weight('semibold')
                                ->searchable()
                                ->size(TextSize::Large)
                                ->formatStateUsing(fn(string $state): string => ucfirst($state))
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
                                ->formatStateUsing(function ($record) {
                                    return $record->is_installed
                                        ? __('plugin-manager::filament/resources/plugin.status.installed')
                                        : __('plugin-manager::filament/resources/plugin.status.not_installed');
                                })
                                ->color(function ($record) {
                                    return $record->is_installed ? 'success' : 'gray';
                                }),

                            TextColumn::make('dependencies_count')
                                ->label(__('plugin-manager::filament/resources/plugin.table.dependencies'))
                                ->state(fn($record) => count($record->getDependenciesFromConfig()))
                                ->badge()
                                ->color('warning')
                                ->suffix(__('plugin-manager::filament/resources/plugin.table.dependencies_suffix'))
                                ->default(0),

                        ]),
                    ]),
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
                    ViewAction::make()
                        ->icon('heroicon-o-eye'),

                    Action::make('install')
                        ->label(__('plugin-manager::filament/resources/plugin.actions.install.title'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn($record) => ! $record->is_installed)
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => __('plugin-manager::filament/resources/plugin.actions.install.heading', ['name' => $record->name]))
                        ->modalDescription(fn($record) => __('plugin-manager::filament/resources/plugin.actions.install.description', ['name' => $record->name]))
                        ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.install.submit'))
                        ->action(function ($record) {
                            $php = escapeshellarg(PHP_BINARY);

                            $artisan = escapeshellarg(base_path('artisan'));

                            $cmd = "$php $artisan $record->name:install";

                            try {
                                exec($cmd, $output, $exitCode);

                                $record->update([
                                    'is_installed' => true,
                                    'is_active'    => true,
                                ]);

                                Notification::make()
                                    ->title(__('plugin-manager::filament/resources/plugin.notifications.installed.title'))
                                    ->body(__('plugin-manager::filament/resources/plugin.notifications.installed.body', ['name' => $record->name]))
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title(__('plugin-manager::filament/resources/plugin.notifications.installed-failed.title'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        })->after(function () {
                            return redirect(self::getUrl('index'));
                        }),

                    Action::make('uninstall')
                        ->label(__('plugin-manager::filament/resources/plugin.actions.uninstall.title'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->modalWidth(Width::ExtraLarge)
                        ->visible(fn($record) => $record->is_installed)
                        ->modalHeading(__('plugin-manager::filament/resources/plugin.actions.uninstall.heading'))
                        ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.uninstall.submit'))
                        ->modalContent(function ($record) {
                            $dependents = $record->getDependentsFromConfig();
                            $package = $record->getPackage();
                            $tables = [];

                            if ($package && ! empty($package->migrationFileNames)) {
                                foreach ($package->migrationFileNames as $migrationFile) {
                                    if (
                                        preg_match('/create_(.*?)_table/', $migrationFile, $matches)
                                    ) {
                                        $table = $matches[1];
                                        $count = \Schema::hasTable($table) ? DB::table($table)->count() : 0;
                                        if ($count > 0) {
                                            $tables[] = [
                                                'table' => $table,
                                                'count' => $count,
                                            ];
                                        }
                                    }
                                }
                            }

                            if (! empty($dependents)) {
                                foreach ($dependents as $dependent) {
                                    $dependentPlugin = Plugin::where('name', $dependent)->first();
                                    $dependentPackage = $dependentPlugin ? $dependentPlugin->getPackage() : null;
                                    if ($dependentPackage && ! empty($dependentPackage->migrationFileNames)) {
                                        foreach ($dependentPackage->migrationFileNames as $migrationFile) {
                                            if (
                                                preg_match('/create_(.*?)_table/', $migrationFile, $matches)
                                            ) {
                                                $table = $matches[1];
                                                $count = \Schema::hasTable($table) ? DB::table($table)->count() : 0;
                                                if ($count > 0) {
                                                    $exists = false;
                                                    foreach ($tables as $existingTable) {
                                                        if ($existingTable['table'] === $table) {
                                                            $exists = true;
                                                            break;
                                                        }
                                                    }
                                                    if (! $exists) {
                                                        $tables[] = [
                                                            'table' => $table,
                                                            'count' => $count,
                                                        ];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            return view('plugin-manager::uninstall-modal', [
                                'record'     => $record,
                                'dependents' => $dependents,
                                'tables'     => $tables,
                            ]);
                        })
                        ->action(function ($record) {
                            $errors = [];

                            $uninstallPackage = function ($pluginName) use (&$errors) {
                                $plugin = Plugin::where('name', $pluginName)->first();
                                if (! $plugin) {
                                    $errors[] = "Plugin '{$pluginName}' not found.";

                                    return;
                                }

                                if (! $plugin->is_installed) {
                                    return;
                                }

                                try {
                                    $package = $plugin->getPackage();
                                    if (! $package) {
                                        $errors[] = "Package for plugin '{$pluginName}' not found.";

                                        return;
                                    }

                                    foreach (array_reverse($package->migrationFileNames) as $migration) {
                                        $migrationPath = base_path("plugins/webkul/{$pluginName}/database/migrations/{$migration}.php");
                                        if (file_exists($migrationPath)) {
                                            $migrationInstance = require $migrationPath;
                                            if (is_object($migrationInstance) && method_exists($migrationInstance, 'down')) {
                                                $migrationInstance->down();
                                            }
                                        }

                                        DB::table('migrations')->where('migration', $migration)->delete();
                                    }

                                    foreach ($package->settingFileNames as $setting) {
                                        $settingPath = base_path("plugins/webkul/{$pluginName}/database/settings/{$setting}.php");
                                        if (file_exists($settingPath)) {
                                            $settingInstance = require $settingPath;
                                            if (is_object($settingInstance) && method_exists($settingInstance, 'down')) {
                                                $settingInstance->down();
                                            }
                                        }

                                        DB::table('migrations')->where('migration', $setting)->delete();
                                    }

                                    $plugin->update([
                                        'is_installed' => false,
                                        'is_active'    => false,
                                    ]);
                                } catch (\Exception $e) {
                                    $errors[] = "Failed to uninstall '{$pluginName}': " . $e->getMessage();
                                }
                            };

                            $dependents = $record->getDependentsFromConfig();
                            if (! empty($dependents)) {
                                foreach ($dependents as $dependent) {
                                    $uninstallPackage($dependent);
                                }
                            }

                            $uninstallPackage($record->name);

                            if (empty($errors)) {
                                $message = "The '{$record->name}' plugin has been uninstalled.";
                                if (! empty($dependents)) {
                                    $dependentsStr = implode(', ', array_map('ucfirst', $dependents));
                                    $message .= " Dependents uninstalled: {$dependentsStr}.";
                                }

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
                        })

                        ->after(function () {
                            return redirect(self::getUrl('index'));
                        }),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->recordActionsAlignment('end')
            ->defaultSort('sort', 'asc')
            ->reorderable('sort')
            ->paginated([16, 24, 32]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plugin Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('plugin-manager::filament/resources/plugin.infolist.name'))
                                    ->formatStateUsing(fn(string $state) => ucfirst($state))
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
                    Section::make('Dependencies')
                        ->schema([
                            RepeatableEntry::make('dependencies')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.dependencies'))
                                ->state(function ($record) {
                                    $dependencies = [];
                                    foreach ($record->getDependenciesFromConfig() as $dep) {
                                        $dependencies[] = [
                                            'name'         => $dep,
                                            'is_installed' => Package::isPluginInstalled($dep),
                                        ];
                                    }

                                    return $dependencies;
                                })
                                ->schema([
                                    TextEntry::make('name')
                                        ->label(__('plugin-manager::filament/resources/plugin.infolist.dependencies-repeater.name'))
                                        ->formatStateUsing(fn(string $state): string => ucfirst($state))
                                        ->badge()
                                        ->color('warning'),

                                    IconEntry::make('is_installed')
                                        ->label(__('plugin-manager::filament/resources/plugin.infolist.dependencies-repeater.is_installed'))
                                        ->boolean()
                                        ->trueIcon('heroicon-s-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                ])
                                ->columns(2)
                                ->placeholder(__('plugin-manager::filament/resources/plugin.infolist.dependencies-repeater.placeholder')),

                            RepeatableEntry::make('dependents')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.dependents-repeater.title'))
                                ->state(function ($record) {
                                    $dependents = [];
                                    foreach ($record->getDependentsFromConfig() as $dep) {
                                        $dependents[] = [
                                            'name'         => $dep,
                                            'is_installed' => Package::isPluginInstalled($dep),
                                        ];
                                    }

                                    return $dependents;
                                })
                                ->schema([
                                    TextEntry::make('name')
                                        ->label(__('plugin-manager::filament/resources/plugin.infolist.dependents-repeater.name'))
                                        ->formatStateUsing(fn(string $state): string => ucfirst($state))
                                        ->badge()
                                        ->color('info'),

                                    IconEntry::make('is_installed')
                                        ->label(__('plugin-manager::filament/resources/plugin.infolist.dependents-repeater.is_installed'))
                                        ->boolean()
                                        ->trueIcon('heroicon-s-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('gray'),
                                ])
                                ->columns(2)
                                ->placeholder(__('plugin-manager::filament/resources/plugin.infolist.dependents-repeater.placeholder')),
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlugins::route('/'),

        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $packages = Plugin::getAllPluginPackages();
        $excludedPlugins = ['accounts', 'products', 'payments', 'full-calendar'];

        $installablePlugins = array_filter(array_keys($packages), function ($pluginName) use ($packages, $excludedPlugins) {
            return ! in_array($pluginName, $excludedPlugins) && ! $packages[$pluginName]->isCore;
        });

        return parent::getEloquentQuery()->whereIn('name', $installablePlugins);
    }
}
