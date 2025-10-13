<?php

namespace Webkul\Inventory\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageOperations extends SettingsPage
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $slug = 'inventory/manage-operations';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    protected static string $settings = OperationSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('inventories::filament/clusters/settings/pages/manage-operations.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('inventories::filament/clusters/settings/pages/manage-operations.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/settings/pages/manage-operations.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('enable_packages')
                    ->label(__('inventories::filament/clusters/settings/pages/manage-operations.form.enable-packages'))
                    ->helperText(function () {
                        $routeBaseName = PackageResource::getRouteBaseName();

                        $url = '#';

                        if (Route::has("{$routeBaseName}.index")) {
                            $url = PackageResource::getUrl();
                        }

                        return new HtmlString(__('inventories::filament/clusters/settings/pages/manage-operations.form.enable-packages-helper-text').'</br><a href="'.$url.'" class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action"><svg style="--c-400:var(--primary-400);--c-600:var(--primary-600)" class="fi-link-icon h-5 w-5 text-custom-600 dark:text-custom-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path></svg><span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--primary-400);--c-600:var(--primary-600)">'.__('inventories::filament/clusters/settings/pages/manage-operations.form.configure-packages').'</span></a>');
                    }),
                TextInput::make('annual_inventory_day')
                    ->label(__('inventories::filament/clusters/settings/pages/manage-operations.form.annual-inventory-day'))
                    ->helperText(__('inventories::filament/clusters/settings/pages/manage-operations.form.annual-inventory-day-helper-text'))
                    ->integer()
                    ->minValue(1)
                    ->maxValue(31)
                    ->required(),
                Select::make('annual_inventory_month')
                    ->label(__('inventories::filament/clusters/settings/pages/manage-operations.form.annual-inventory-month'))
                    ->options([
                        1  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.january'),
                        2  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.february'),
                        3  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.march'),
                        4  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.april'),
                        5  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.may'),
                        6  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.june'),
                        7  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.july'),
                        8  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.august'),
                        9  => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.september'),
                        10 => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.october'),
                        11 => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.november'),
                        12 => __('inventories::filament/clusters/settings/pages/manage-operations.form.months.december'),
                    ])
                    ->helperText(__('inventories::filament/clusters/settings/pages/manage-operations.form.annual-inventory-month-helper-text'))
                    ->required(),
            ]);
    }
}
