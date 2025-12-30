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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DBSchema;
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    ImageColumn::make('id')
                        ->label('')
                        ->getStateUsing(fn ($record) => $record->getPackage()?->icon
                            ? asset("svg/{$record->getPackage()->icon}.svg")
                            : 'heroicon-o-puzzle-piece')
                        ->imageSize(100)
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

                    self::installAction(),
                    self::uninstallAction(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->recordActionsAlignment('end')
            ->defaultSort('sort', 'asc')
            ->reorderable('sort')
            ->paginated([16, 24, 32]);
    }

    protected static function installAction(): Action
    {
        return Action::make('install')
            ->label(__('plugin-manager::filament/resources/plugin.actions.install.title'))
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->visible(fn ($record) => ! $record->is_installed)
            ->requiresConfirmation()
            ->modalHeading(fn ($record) => __('plugin-manager::filament/resources/plugin.actions.install.heading', ['name' => $record->name]))
            ->modalDescription(fn ($record) => __('plugin-manager::filament/resources/plugin.actions.install.description', ['name' => $record->name]))
            ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.install.submit'))
            ->action(function ($record) {
                try {
                    $cmd = sprintf(
                        '%s %s %s:install',
                        escapeshellarg(PHP_BINARY),
                        escapeshellarg(base_path('artisan')),
                        $record->name
                    );

                    exec($cmd);

                    $record->update(['is_installed' => true, 'is_active' => true]);

                    Notification::make()
                        ->title(__('plugin-manager::filament/resources/plugin.notifications.installed.title'))
                        ->body(__('plugin-manager::filament/resources/plugin.notifications.installed.body', ['name' => $record->name]))
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('plugin-manager::filament/resources/plugin.notifications.installed-failed.title'))
                        ->body($e->getMessage())
                        ->danger()
                        ->persistent()
                        ->send();
                }
            })
            ->after(fn () => redirect(self::getUrl('index')));
    }

    protected static function uninstallAction(): Action
    {
        return Action::make('uninstall')
            ->label(__('plugin-manager::filament/resources/plugin.actions.uninstall.title'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->modalWidth(Width::ExtraLarge)
            ->visible(fn ($record) => $record->is_installed)
            ->modalHeading(__('plugin-manager::filament/resources/plugin.actions.uninstall.heading'))
            ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.uninstall.submit'))
            ->modalContent(fn ($record) => self::buildUninstallModal($record))
            ->action(fn ($record) => self::handleUninstall($record))
            ->after(fn () => redirect(self::getUrl('index')));
    }

    protected static function buildUninstallModal($record)
    {
        $tables = collect();
        $dependents = $record->getDependentsFromConfig();
        $packages = collect([$record->name => $record->getPackage()])
            ->merge($dependents ? collect($dependents)->mapWithKeys(fn ($dep) => [$dep => Plugin::where('name', $dep)->first()?->getPackage()]) : []);

        $packages->each(function ($package) use (&$tables) {

            if (
                ! $package
                || empty($package->migrationFileNames)
            ) {
                return;
            }

            collect($package->migrationFileNames)->each(function ($migrationFile) use (&$tables) {

                if (preg_match('/create_(.*?)_table/', $migrationFile, $matches)) {
                    $table = $matches[1];
                    $count = DBSchema::hasTable($table) ? DB::table($table)->count() : 0;

                    if (
                        $count > 0 &&
                        $tables->where('table', $table)->isEmpty()
                    ) {
                        $tables->push(['table' => $table, 'count' => $count]);
                    }
                }
            });
        });

        return view('plugin-manager::uninstall-modal', compact('record', 'dependents', 'tables'));
    }

    protected static function handleUninstall($record)
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
                    $package = $plugin->getPackage();

                    if (! $package) {
                        throw new \Exception("Package for '{$pluginName}' not found.");
                    }

                    // Run migration and settings downs
                    self::runDownMigrations($pluginName, $package->migrationFileNames, 'migrations');
                    self::runDownMigrations($pluginName, $package->settingFileNames, 'settings');

                    $plugin->update(['is_installed' => false, 'is_active' => false]);
                } catch (\Throwable $e) {
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

    protected static function runDownMigrations(string $pluginName, array $fileNames, string $type): void
    {
        collect($fileNames)
            ->reverse()
            ->each(function ($file) use ($pluginName, $type) {
                $path = base_path("plugins/webkul/{$pluginName}/database/{$type}/{$file}.php");

                if (! file_exists($path)) {
                    return;
                }

                $instance = require $path;

                if (is_object($instance) && method_exists($instance, 'down')) {
                    $instance->down();
                }

                DB::table('migrations')->where('migration', $file)->delete();
            });
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

    public static function getEloquentQuery(): Builder
    {
        $excluded = ['accounts', 'products', 'payments', 'full-calendar'];

        $installable = collect(Plugin::getAllPluginPackages())
            ->reject(fn ($pkg, $name) => in_array($name, $excluded)
                || $pkg->isCore)
            ->keys();

        return parent::getEloquentQuery()->whereIn('name', $installable);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlugins::route('/'),
        ];
    }
}
