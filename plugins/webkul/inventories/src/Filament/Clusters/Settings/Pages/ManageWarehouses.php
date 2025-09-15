<?php

namespace Webkul\Inventory\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageWarehouses extends SettingsPage
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $slug = 'inventory/manage-warehouses';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 3;

    protected static string $settings = WarehouseSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('inventories::filament/clusters/settings/pages/manage-warehouses.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('inventories::filament/clusters/settings/pages/manage-warehouses.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/settings/pages/manage-warehouses.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('enable_locations')
                    ->label(__('inventories::filament/clusters/settings/pages/manage-warehouses.form.enable-locations'))
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        if (! $get('enable_locations')) {
                            $set('enable_multi_steps_routes', false);
                        }
                    })
                    ->helperText(function () {
                        $routeBaseName = LocationResource::getRouteBaseName();

                        $url = '#';

                        if (Route::has("{$routeBaseName}.index")) {
                            $url = LocationResource::getUrl();
                        }

                        return new HtmlString(__('inventories::filament/clusters/settings/pages/manage-warehouses.form.enable-locations-helper-text').'</br><a href="'.$url.'" class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action"><svg style="--c-400:var(--primary-400);--c-600:var(--primary-600)" class="fi-link-icon h-5 w-5 text-custom-600 dark:text-custom-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path></svg><span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--primary-400);--c-600:var(--primary-600)">'.__('inventories::filament/clusters/settings/pages/manage-warehouses.form.configure-locations').'</span></a>');
                    })
                    ->live(),
                Toggle::make('enable_multi_steps_routes')
                    ->label(__('inventories::filament/clusters/settings/pages/manage-warehouses.form.enable-multi-steps-routes'))
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        if ($get('enable_multi_steps_routes')) {
                            $set('enable_locations', true);
                        }
                    })
                    ->helperText(new HtmlString(__('inventories::filament/clusters/settings/pages/manage-warehouses.form.enable-multi-steps-routes-helper-text').'</br><a href="'.WarehouseResource::getUrl().'" class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action"><svg style="--c-400:var(--primary-400);--c-600:var(--primary-600)" class="fi-link-icon h-5 w-5 text-custom-600 dark:text-custom-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path></svg><span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--primary-400);--c-600:var(--primary-600)">'.__('inventories::filament/clusters/settings/pages/manage-warehouses.form.configure-routes').'</span></a>'))
                    ->live(),
            ]);
    }

    protected function beforeSave(): void
    {
        if (Warehouse::count() > 1) {
            Notification::make()
                ->warning()
                ->title(__('inventories::filament/clusters/settings/pages/manage-warehouses.before-save.notification.warning.title'))
                ->body(__('inventories::filament/clusters/settings/pages/manage-warehouses.before-save.notification.warning.body'))
                ->send();

            $this->fillForm();

            $this->halt();
        }
    }

    protected function afterSave(): void
    {
        foreach (Warehouse::all() as $warehouse) {
            OperationType::withTrashed()
                ->whereIn('id', [$warehouse->internal_type_id])
                ->update(['deleted_at' => $this->data['enable_locations'] ? null : now()]);
        }
    }
}
