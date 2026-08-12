<?php

namespace Webkul\Support\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Settings\BrandSettings;

class ManageBranding extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Settings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = 10;

    protected static string $settings = BrandSettings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_support_manage_branding';
    }

    public static function getNavigationGroup(): string
    {
        return __('support::filament/clusters/manage-branding.group');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('support::filament/clusters/manage-branding.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('support::filament/clusters/manage-branding.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/clusters/manage-branding.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('support::filament/clusters/manage-branding.form.sections.logo.title'))
                    ->description(__('support::filament/clusters/manage-branding.form.sections.logo.description'))
                    ->columns(2)
                    ->schema([
                        FileUpload::make('light_logo')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.light-logo'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.light-logo-helper'))
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        FileUpload::make('dark_logo')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.dark-logo'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.dark-logo-helper'))
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        FileUpload::make('favicon')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.favicon'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.favicon-helper'))
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        TextInput::make('logo_height')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.logo-height'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.logo-height-helper'))
                            ->placeholder('2rem'),
                    ]),
                Section::make(__('support::filament/clusters/manage-branding.form.sections.colors.title'))
                    ->description(__('support::filament/clusters/manage-branding.form.sections.colors.description'))
                    ->columns(3)
                    ->headerActions([
                        Action::make('reset')
                            ->label(__('support::filament/clusters/manage-branding.actions.reset.label'))
                            ->icon('heroicon-o-arrow-path')
                            ->color('gray')
                            ->link()
                            ->requiresConfirmation()
                            ->action(function (Set $set): void {
                                foreach ($this->getDefaultColors() as $field => $hex) {
                                    $set($field, $hex);
                                }
                            }),
                    ])
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.primary-color'))
                            ->hexColor(),
                        ColorPicker::make('gray_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.gray-color'))
                            ->hexColor(),
                        ColorPicker::make('danger_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.danger-color'))
                            ->hexColor(),
                        ColorPicker::make('info_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.info-color'))
                            ->hexColor(),
                        ColorPicker::make('success_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.success-color'))
                            ->hexColor(),
                        ColorPicker::make('warning_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.warning-color'))
                            ->hexColor(),
                    ]),
            ]);
    }

    private function getDefaultColors(): array
    {
        return [
            'primary_color' => Color::convertToHex(Color::Blue[600]),
            'gray_color'    => Color::convertToHex(Color::Zinc[600]),
            'danger_color'  => Color::convertToHex(Color::Red[600]),
            'info_color'    => Color::convertToHex(Color::Blue[600]),
            'success_color' => Color::convertToHex(Color::Green[600]),
            'warning_color' => Color::convertToHex(Color::Amber[600]),
        ];
    }
}
