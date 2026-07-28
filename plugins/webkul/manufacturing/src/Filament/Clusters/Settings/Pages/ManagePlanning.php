<?php

namespace Webkul\Manufacturing\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Webkul\Manufacturing\Settings\PlanningSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManagePlanning extends SettingsPage
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $slug = 'manufacturing/manage-planning';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $settings = PlanningSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_manufacturing_manage_planning';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('manufacturing::filament/clusters/settings/pages/manage-planning.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('manufacturing::filament/clusters/settings/pages/manage-planning.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/settings/pages/manage-planning.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('manufacturing_lead')
                    ->label(__('manufacturing::filament/clusters/settings/pages/manage-planning.form.manufacturing-lead-time.label'))
                    ->helperText(__('manufacturing::filament/clusters/settings/pages/manage-planning.form.manufacturing-lead-time.helper-text'))
                    ->integer()
                    ->minValue(0)
                    ->required(),
            ]);
    }
}
