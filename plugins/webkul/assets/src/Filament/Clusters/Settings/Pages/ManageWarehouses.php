<?php

namespace Webkul\Asset\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use UnitEnum;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Schemas\AssetWarehouseForm;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageWarehouses extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = AssetSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'assets/manage-warehouses';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('assets::filament/pages/manage-warehouses.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/pages/manage-warehouses.navigation.title');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('assets::filament/pages/manage-warehouses.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('assets::filament/pages/manage-warehouses.title');
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_asset_manage_assets';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('assets::filament/pages/manage-assets.form.sections.warehouses_and_operation.title'))
                    ->description(__('assets::filament/pages/manage-assets.form.sections.warehouses_and_operation.description'))
                    ->schema([
                        Select::make('default_warehouse_id')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.default-warehouse'))
                            ->helperText(function (): HtmlString {
                                $text = __('assets::filament/pages/manage-assets.form.helpers.default-warehouse');
                                $routeBase = AssetWarehouseResource::getRouteBaseName();
                                if (Route::has("{$routeBase}.index")) {
                                    $url = AssetWarehouseResource::getUrl();
                                    $linkLabel = __('assets::filament/pages/manage-assets.form.helpers.manage-warehouses-link');
                                    $link = '<br><a href="'.$url.'" class="fi-link text-primary-600 dark:text-primary-400 text-xs font-semibold hover:underline">'.$linkLabel.' &rarr;</a>';

                                    return new HtmlString($text.$link);
                                }

                                return new HtmlString($text);
                            })
                            ->options(fn (): array => static::activeWarehouseOptions())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->createOptionForm(fn (Schema $schema): Schema => AssetWarehouseForm::configure($schema))
                            ->createOptionAction(fn ($action) => $action
                                ->visible(fn (): bool => AssetWarehouseResource::canCreate())
                                ->authorize(fn (): bool => AssetWarehouseResource::canCreate()))
                            ->createOptionUsing(fn (array $data): int => AssetWarehouse::query()->create($data)->id),
                        Toggle::make('enable_site_assets')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.enable-site-assets'))
                            ->helperText(function (): HtmlString {
                                $text = __('assets::filament/pages/manage-assets.form.helpers.enable-site-assets');
                                $routeBase = AssetSiteResource::getRouteBaseName();
                                if (Route::has("{$routeBase}.index")) {
                                    $url = AssetSiteResource::getUrl();
                                    $linkLabel = __('assets::filament/pages/manage-assets.form.helpers.manage-sites-link');
                                    $link = '<br><a href="'.$url.'" class="fi-link text-primary-600 dark:text-primary-400 text-xs font-semibold hover:underline">'.$linkLabel.' &rarr;</a>';

                                    return new HtmlString($text.$link);
                                }

                                return new HtmlString($text);
                            })
                            ->live(),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * @return array<int|string, string>
     */
    protected static function activeWarehouseOptions(): array
    {
        return AssetWarehouse::query()
            ->where('is_active', true)
            ->where(owned_by_company())
            ->pluck('name', 'id')
            ->all();
    }

    protected function beforeSave(): void
    {
        if (isset($this->data['enable_site_assets']) && ! (bool) $this->data['enable_site_assets']) {
            $hasHeldSiteAssets = Asset::withoutGlobalScopes()
                ->where('status', AssetStatus::Held)
                ->whereNull('current_employee_id')
                ->whereNotNull('current_site_id')
                ->exists();

            if ($hasHeldSiteAssets) {
                Notification::make()
                    ->warning()
                    ->title(__('assets::filament/pages/manage-assets.warnings.site-assets-held'))
                    ->send();

                $this->fillForm();

                $this->halt();
            }
        }
    }
}
