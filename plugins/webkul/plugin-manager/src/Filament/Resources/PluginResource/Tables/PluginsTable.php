<?php

namespace Webkul\PluginManager\Filament\Resources\PluginResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
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
use Webkul\PluginManager\Filament\Resources\PluginResource;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

class PluginsTable
{
    public static function configure(Table $table): Table
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
                                ->formatStateUsing(fn (string $state) => PluginResource::localize('names', $state, ucfirst($state)))
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
                            ->wrap()
                            ->formatStateUsing(fn ($state, $record) => PluginResource::localize('summaries', $record->name, $state)),

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
                                $phpPath = PluginResource::getPhpExecutablePath();

                                $php = escapeshellarg($phpPath);

                                $artisan = escapeshellarg(base_path('artisan'));

                                $commandName = escapeshellarg("{$record->name}:install");

                                $cmd = PluginResource::buildTimeoutCommand(300, "$php $artisan $commandName --no-interaction 2>&1");

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
                        ->after(fn () => redirect(PluginResource::getUrl('index'))),

                    Action::make('uninstall')
                        ->label(__('plugin-manager::filament/resources/plugin.actions.uninstall.title'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->modalWidth(Width::ExtraLarge)
                        ->visible(fn ($record) => $record->is_installed)
                        ->modalHeading(__('plugin-manager::filament/resources/plugin.actions.uninstall.heading'))
                        ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin.actions.uninstall.submit'))
                        ->modalSubmitAction(
                            fn ($action, $record) => $action->hidden(
                                collect($record->getDependentsFromConfig())
                                    ->contains(fn ($dependent) => Package::isPluginInstalled($dependent))
                            )
                        )
                        ->modalContent(function ($record) {
                            $dependents = $record->getDependentsFromConfig();

                            $installedDependents = collect($dependents)
                                ->filter(fn ($dependent) => Package::isPluginInstalled($dependent))
                                ->values();

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

                            return view('plugin-manager::uninstall-modal', compact('record', 'dependents', 'installedDependents', 'tables'));
                        })
                        ->action(fn ($record) => PluginResource::uninstallPlugin($record))
                        ->after(fn () => redirect(PluginResource::getUrl('index'))),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->recordActionsAlignment('end')
            ->defaultSort('sort', 'asc')
            ->paginated([16, 24, 32]);
    }
}
