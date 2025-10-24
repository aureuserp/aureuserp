<?php

namespace Webkul\PluginManager\Filament\Resources\PluginResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\PluginManager\Filament\Resources\PluginResource;
use Webkul\Support\Models\Plugin;

class ListPlugins extends ListRecords
{
    protected static string $resource = PluginResource::class;

    public function getTabs(): array
    {
        $packages = Plugin::getAllPluginPackages();
        $excludedPlugins = ['accounts', 'products', 'payments', 'full-calendar'];

        $installablePlugins = array_filter(array_keys($packages), function ($pluginName) use ($packages, $excludedPlugins) {
            return ! in_array($pluginName, $excludedPlugins) && ! $packages[$pluginName]->isCore;
        });

        return [
            'all' => Tab::make('All Plugins')
                ->badge(Plugin::whereIn('name', $installablePlugins)->count())
                ->modifyQueryUsing(fn ($query) => $query->whereIn('name', $installablePlugins)),

            'installed' => Tab::make('Installed')
                ->badge(Plugin::whereIn('name', $installablePlugins)->where('is_installed', true)->count())
                ->modifyQueryUsing(
                    fn ($query) => $query->where('is_installed', true)->whereIn('name', $installablePlugins)
                ),

            'not_installed' => Tab::make('Not Installed')
                ->badge(Plugin::whereIn('name', $installablePlugins)->where('is_installed', false)->count())
                ->modifyQueryUsing(
                    fn ($query) => $query->where('is_installed', false)->whereIn('name', $installablePlugins)
                ),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_plugins')
                ->label('Sync Available Plugins')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Sync Plugins')
                ->modalDescription('This will scan the plugins directory and register any new plugins found.')
                ->modalSubmitActionLabel('Sync Plugins')
                ->action(function () {
                    try {
                        $excludedPlugins = ['accounts', 'products', 'payments', 'full-calendar'];
                        $packages = Plugin::getAllPluginPackages();
                        $synced = 0;

                        foreach ($packages as $pluginName => $package) {
                            if ($package->isCore || in_array($pluginName, $excludedPlugins)) {
                                continue;
                            }

                            $composerPath = base_path("plugins/webkul/{$pluginName}/composer.json");
                            $composerData = file_exists($composerPath)
                                ? json_decode(file_get_contents($composerPath), true)
                                : [];

                            $plugin = Plugin::updateOrCreate(
                                ['name' => $pluginName],
                                [
                                    'author'         => $composerData['authors'][0]['name'] ?? 'Webkul',
                                    'summary'        => $composerData['description'] ?? $package->description ?? '',
                                    'description'    => $composerData['description'] ?? $package->description ?? '',
                                    'latest_version' => $composerData['version'] ?? '1.0.0',
                                    'license'        => $composerData['license'] ?? 'MIT',
                                    'is_active'      => true,
                                    'is_installed'   => false,
                                    'sort'           => 1,
                                ]
                            );

                            $dependencies = $plugin->getDependenciesFromConfig();
                            if (! empty($dependencies)) {
                                $plugin->dependencies()->sync(
                                    Plugin::whereIn('name', $dependencies)->pluck('id')
                                );
                            }

                            if ($plugin->wasRecentlyCreated) {
                                $synced++;
                            }
                        }

                        Notification::make()
                            ->title('Plugins Synced Successfully')
                            ->body("Found and synced {$synced} new plugin(s).")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Sync Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

        ];
    }
}
