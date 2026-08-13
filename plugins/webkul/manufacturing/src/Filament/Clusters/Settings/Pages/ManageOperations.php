<?php

namespace Webkul\Manufacturing\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageOperations extends SettingsPage
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog';

    protected static ?string $slug = 'manufacturing/manage-operations';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing';

    protected static ?int $navigationSort = 1;

    protected static string $settings = OperationSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_manufacturing_manage_operations';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('manufacturing::filament/clusters/settings/pages/manage-operations.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('manufacturing::filament/clusters/settings/pages/manage-operations.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/settings/pages/manage-operations.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('enable_work_orders')
                    ->label(__('manufacturing::filament/clusters/settings/pages/manage-operations.form.enable-work-orders.label'))
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        if (! $get('enable_work_orders')) {
                            $set('enable_work_order_dependencies', false);
                        }
                    })
                    ->helperText(function () {
                        $routeBaseName = WorkCenterResource::getRouteBaseName();

                        $url = '#';

                        if (Route::has("{$routeBaseName}.index")) {
                            $url = WorkCenterResource::getUrl();
                        }

                        return new HtmlString(__('manufacturing::filament/clusters/settings/pages/manage-operations.form.enable-work-orders.helper-text').'</br><a href="'.$url.'" class="fi-link group/link fi-size-md fi-link-size-md fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action relative inline-flex items-center justify-center gap-1.5 outline-none"><svg style="--c-400:var(--primary-400);--c-600:var(--primary-600)" class="fi-link-icon text-custom-600 dark:text-custom-400 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path></svg><span class="text-custom-600 dark:text-custom-400 text-sm font-semibold group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--primary-400);--c-600:var(--primary-600)">'.__('manufacturing::filament/clusters/settings/pages/manage-operations.form.enable-work-orders.link-text').'</span></a>');
                    })
                    ->live(),
                Toggle::make('enable_work_order_dependencies')
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        if ($get('enable_work_order_dependencies')) {
                            $set('enable_work_orders', true);
                        }
                    })
                    ->label(__('manufacturing::filament/clusters/settings/pages/manage-operations.form.enable-work-order-dependencies.label'))
                    ->helperText(__('manufacturing::filament/clusters/settings/pages/manage-operations.form.enable-work-order-dependencies.helper-text'))
                    ->live(),
                Toggle::make('enable_byproducts')
                    ->hidden()
                    ->label(__('manufacturing::filament/clusters/settings/pages/manage-operations.form.enable-byproducts.label'))
                    ->helperText(__('manufacturing::filament/clusters/settings/pages/manage-operations.form.enable-byproducts.helper-text')),
            ]);
    }
}
