<?php

namespace Webkul\Asset\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageGovernance extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = AssetSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'assets/manage-governance';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('assets::filament/pages/manage-governance.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/pages/manage-governance.navigation.title');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('assets::filament/pages/manage-governance.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('assets::filament/pages/manage-governance.title');
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_asset_manage_assets';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('assets::filament/pages/manage-assets.form.sections.governance_and_attachments.title'))
                    ->description(__('assets::filament/pages/manage-assets.form.sections.governance_and_attachments.description'))
                    ->schema([
                        Toggle::make('require_attachments_on_custody')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.require-attachments-on-custody'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.require-attachments-on-custody')),
                        Toggle::make('require_attachments_on_damage')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.require-attachments-on-damage'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.require-attachments-on-damage')),
                        Toggle::make('require_attachments_on_termination')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.require-attachments-on-termination'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.require-attachments-on-termination')),
                        Toggle::make('restrict_termination_to_managers')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.restrict-termination-to-managers'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.restrict-termination-to-managers')),
                    ])
                    ->columns(2),
            ]);
    }
}
