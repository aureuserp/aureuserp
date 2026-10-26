<?php

namespace Webkul\Field;

use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Models\Field;
use Webkul\Field\Policies\FieldPolicy;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Support\Support\VersionedCss;

class FieldServiceProvider extends PackageServiceProvider
{
    public static string $name = 'fields';

    public static string $viewNamespace = 'fields';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2024_11_13_052541_create_custom_fields_table',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        $this->registerProgressStepperSlot();

        Gate::policy(Field::class, FieldPolicy::class);
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(FieldsPlugin::make());
        });
    }

    public function registerCustomCss()
    {
        FilamentAsset::register([
            VersionedCss::make('fields', __DIR__.'/../resources/dist/fields.css'),
        ], 'fields');
    }

    protected function registerProgressStepperSlot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_SUB_NAVIGATION_TOP_AFTER,
            fn (): HtmlString => new HtmlString(ProgressStepper::getSubNavigationSlotHtml()),
        );
    }
}
