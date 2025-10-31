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
        $excluded = ['accounts', 'products', 'payments', 'full-calendar'];

        $packages = collect(Plugin::getAllPluginPackages())
            ->reject(fn ($pkg, $name) => $pkg->isCore || in_array($name, $excluded))
            ->keys();

        $query = fn ($installed = null) => Plugin::whereIn('name', $packages)
            ->when(! is_null($installed), fn ($q) => $q->where('is_installed', $installed));

        return [
            'all' => Tab::make(__('All Plugins'))
                ->badge($query()->count())
                ->modifyQueryUsing(fn ($query) => $query->whereIn('name', $packages)),

            'installed' => Tab::make(__('Installed'))
                ->badge($query(true)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('is_installed', true)->whereIn('name', $packages)),

            'not_installed' => Tab::make(__('Not Installed'))
                ->badge($query(false)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('is_installed', false)->whereIn('name', $packages)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_plugins')
                ->label(__('Sync Available Plugins'))
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading(__('Sync Plugins'))
                ->modalDescription(__('This will scan and register any new plugins found.'))
                ->modalSubmitActionLabel(__('Sync Plugins'))
                ->action(fn () => $this->syncPlugins()),
        ];
    }

    protected function syncPlugins(): void
    {
        try {
            $excluded = ['accounts', 'products', 'payments', 'full-calendar'];

            $packages = collect(Plugin::getAllPluginPackages())
                ->reject(fn ($package, $name) => $package->isCore || in_array($name, $excluded));

            $synced = 0;

            $packages->each(function ($package, $name) use (&$synced) {

                $composerPath = base_path("plugins/webkul/{$name}/composer.json");

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
                    $plugin->dependencies()->sync(
                        Plugin::whereIn('name', $deps)->pluck('id')
                    );
                }

                if ($plugin->wasRecentlyCreated) {
                    $synced++;
                }
            });

            Notification::make()
                ->title(__('Plugins Synced Successfully'))
                ->body(__('Found and synced :count new plugin(s).', ['count' => $synced]))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title(__('Sync Failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
