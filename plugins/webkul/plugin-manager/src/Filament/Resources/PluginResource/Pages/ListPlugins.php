<?php

namespace Webkul\PluginManager\Filament\Resources\PluginResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Throwable;
use Webkul\PluginManager\Filament\Resources\PluginResource;
use Webkul\PluginManager\Models\Plugin;

class ListPlugins extends ListRecords
{
    protected static string $resource = PluginResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('plugin-manager::filament/resources/plugin/pages/list-plugins.navigation.title');
    }

    public function getTabs(): array
    {
        $extra = [];

        foreach (Plugin::getAllPluginPackages() as $key => $package) {
            if ($package->icon) {
                continue;
            }

            $extra[] = $key;
        }

        return [
            'apps' => Tab::make(__('plugin-manager::filament/resources/plugin/pages/list-plugins.tabs.apps'))
                ->badge(Plugin::whereNotIn('name', $extra)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('name', $extra)),

            'extra' => Tab::make(__('plugin-manager::filament/resources/plugin/pages/list-plugins.tabs.extra'))
                ->badge(Plugin::whereIn('name', $extra)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('name', $extra)),

            'installed' => Tab::make(__('plugin-manager::filament/resources/plugin/pages/list-plugins.tabs.installed'))
                ->badge(Plugin::where('is_installed', true)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_installed', true)),

            'not_installed' => Tab::make(__('plugin-manager::filament/resources/plugin/pages/list-plugins.tabs.not-installed'))
                ->badge(Plugin::where('is_installed', false)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_installed', false)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_plugins')
                ->label(__('plugin-manager::filament/resources/plugin/pages/list-plugins.header-actions.sync.label'))
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading(__('plugin-manager::filament/resources/plugin/pages/list-plugins.header-actions.sync.modal-heading'))
                ->modalDescription(__('plugin-manager::filament/resources/plugin/pages/list-plugins.header-actions.sync.modal-description'))
                ->modalSubmitActionLabel(__('plugin-manager::filament/resources/plugin/pages/list-plugins.header-actions.sync.modal-submit-action-label'))
                ->action(fn () => $this->syncPlugins()),
        ];
    }

    protected function syncPlugins(): void
    {
        try {
            $synced = collect(Plugin::getAllPluginPackages())
                ->filter(function ($package, $name) {
                    $composerPath = $package->basePath('composer.json');

                    $composer = file_exists($composerPath)
                        ? json_decode(file_get_contents($composerPath), true) ?? []
                        : [];

                    $plugin = Plugin::updateOrCreate(
                        ['name' => $name],
                        [
                            'author'         => data_get($composer, 'authors.0.name', 'Webkul'),
                            'summary'        => data_get($composer, 'description', $package->description ?? ''),
                            'description'    => data_get($composer, 'description', $package->description ?? ''),
                            'latest_version' => data_get($composer, 'version', '1.0.0'),
                            'license'        => data_get($composer, 'license', 'MIT'),
                        ]
                    );

                    if ($deps = $plugin->getDependenciesFromConfig()) {
                        $dependencyIds = Plugin::whereIn('name', $deps)->pluck('id');

                        $plugin->dependencies()->sync($dependencyIds);
                    }

                    return $plugin->wasRecentlyCreated;
                })
                ->count();

            Notification::make()
                ->title(__('plugin-manager::filament/resources/plugin/pages/list-plugins.header-actions.sync.notification.success.title'))
                ->body(__('plugin-manager::filament/resources/plugin/pages/list-plugins.header-actions.sync.notification.success.body', [
                    'count' => $synced,
                ]))
                ->success()
                ->send();
        } catch (Throwable $e) {
            report($e);

            Notification::make()
                ->title(__('plugin-manager::filament/resources/plugin/pages/list-plugins.header-actions.sync.notification.error.title'))
                ->body(__('plugin-manager::filament/resources/plugin/pages/list-plugins.header-actions.sync.notification.error.body', [
                    'error' => $e->getMessage(),
                ]))
                ->danger()
                ->send();
        }
    }
}
