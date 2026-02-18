<?php

namespace Webkul\Accounting;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Widgets\JournalChartWidget;
use Webkul\Accounting\Livewire\InvoiceSummary;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class AccountingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'accounting';

    public static string $viewNamespace = 'accounting';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies([
                'accounts',
            ])
            ->icon('accounting')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->installDependencies();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {});
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        $this->registerLivewireComponents();
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(AccountingPlugin::make());
        });
    }

    public function registerLivewireComponents()
    {
        Livewire::component('accounting::journal-chart', JournalChartWidget::class);

        Livewire::component('accounting-invoice-summary', InvoiceSummary::class);
    }

    public function registerCustomCss()
    {
        FilamentAsset::register([
            Css::make('accounting', __DIR__.'/../resources/dist/accounting.css'),
        ], 'accounting');
    }
}
