<?php

namespace Webkul\Asset\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageCustody extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = AssetSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'assets/manage-custody';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('assets::filament/pages/manage-custody.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/pages/manage-custody.navigation.title');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('assets::filament/pages/manage-custody.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('assets::filament/pages/manage-custody.title');
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_asset_manage_assets';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('assets::filament/pages/manage-assets.form.sections.custody_and_duration.title'))
                    ->description(__('assets::filament/pages/manage-assets.form.sections.custody_and_duration.description'))
                    ->schema([
                        Toggle::make('require_site_on_custody')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.require-site-on-custody'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.require-site-on-custody')),
                        TextInput::make('default_custody_duration_days')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.default-custody-duration-days'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.default-custody-duration-days'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(3650)
                            ->nullable(),
                        Toggle::make('require_expected_return_date')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.require-expected-return-date'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.require-expected-return-date')),
                        Toggle::make('prevent_duplicate_product_custody')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.prevent-duplicate-product-custody'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.prevent-duplicate-product-custody')),
                    ])
                    ->columns(2),

                Section::make(__('assets::filament/pages/manage-assets.form.sections.consumables_rules.title'))
                    ->description(__('assets::filament/pages/manage-assets.form.sections.consumables_rules.description'))
                    ->schema([
                        Toggle::make('enable_consumables_batch_issue')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.enable-consumables-batch-issue'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.enable-consumables-batch-issue')),

                        Toggle::make('auto_fill_consumable_employees_by_site')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.auto-fill-consumable-employees-by-site'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.auto-fill-consumable-employees-by-site')),

                        TextInput::make('default_consumable_issue_quantity')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.default-consumable-issue-quantity'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.default-consumable-issue-quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10000)
                            ->default(1)
                            ->required(),

                        Toggle::make('require_site_on_consumable_issue')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.require-site-on-consumable-issue'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.require-site-on-consumable-issue')),

                        Toggle::make('require_attachments_on_consumable_issue')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.require-attachments-on-consumable-issue'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.require-attachments-on-consumable-issue')),
                    ])
                    ->columns(2),
            ]);
    }
}
