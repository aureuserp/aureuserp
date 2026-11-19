<?php

namespace Webkul\Asset\Filament\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use UnitEnum;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\Sequence;

class ManageAssets extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = AssetSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'assets/manage-assets';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('assets::filament/pages/manage-assets.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/pages/manage-assets.navigation.title');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('assets::filament/pages/manage-assets.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('assets::filament/pages/manage-assets.title');
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_asset_manage_assets';
    }

    protected static function hasRegisteredAssets(): bool
    {
        return Asset::withoutGlobalScopes()->withTrashed()->exists();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('assets::filament/pages/manage-assets.form.sections.general.title'))
                    ->description(__('assets::filament/pages/manage-assets.form.sections.general.description'))
                    ->schema([
                        Toggle::make('auto_generate_tag')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.auto-generate-tag'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.auto-generate-tag'))
                            ->live(),
                        TextInput::make('tag_prefix')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.tag-prefix'))
                            ->helperText(fn (): string => static::hasRegisteredAssets()
                                ? __('assets::filament/pages/manage-assets.form.helpers.tag-prefix-locked')
                                : __('assets::filament/pages/manage-assets.form.helpers.tag-prefix'))
                            ->disabled(fn (): bool => static::hasRegisteredAssets())
                            ->dehydrated(fn (): bool => ! static::hasRegisteredAssets())
                            ->visible(fn (Get $get): bool => (bool) $get('auto_generate_tag'))
                            ->required()
                            ->maxLength(20)
                            ->live(),
                        TextInput::make('tag_padding')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.tag-padding'))
                            ->helperText(function (Get $get): string {
                                $prefix = (string) ($get('tag_prefix') ?: 'AST-');
                                $padding = max(3, min(8, (int) ($get('tag_padding') ?: 5)));
                                $preview = $prefix.str_pad('1', $padding, '0', STR_PAD_LEFT);

                                return __('assets::filament/pages/manage-assets.form.helpers.tag-padding', ['preview' => $preview]);
                            })
                            ->numeric()
                            ->minValue(3)
                            ->maxValue(8)
                            ->default(5)
                            ->disabled(fn (): bool => static::hasRegisteredAssets())
                            ->dehydrated(fn (): bool => ! static::hasRegisteredAssets())
                            ->visible(fn (Get $get): bool => (bool) $get('auto_generate_tag'))
                            ->required()
                            ->live(),
                        Select::make('default_condition')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.default-condition'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.default-condition'))
                            ->options(AssetCondition::class)
                            ->dehydrateStateUsing(fn (mixed $state): ?string => $state instanceof BackedEnum ? $state->value : ($state ? (string) $state : null))
                            ->required(),
                        Toggle::make('require_serial_by_default')
                            ->label(__('assets::filament/pages/manage-assets.form.fields.require-serial-by-default'))
                            ->helperText(__('assets::filament/pages/manage-assets.form.helpers.require-serial-by-default')),
                    ])
                    ->columns(2),
            ]);
    }

    protected function afterSave(): void
    {
        if (static::hasRegisteredAssets()) {
            return;
        }

        $settings = app(AssetSettings::class);
        $prefix = $this->data['tag_prefix'] ?? $settings->tag_prefix;
        $padding = max(3, min(8, (int) ($this->data['tag_padding'] ?? $settings->tag_padding ?? 5)));

        Sequence::withoutGlobalScope(CompanyScope::class)
            ->where('code', AssetCustodyService::SEQUENCE_CODE)
            ->when(current_company_id(), fn ($query, $companyId) => $query->where('company_id', $companyId))
            ->update([
                'prefix'  => $prefix,
                'padding' => $padding,
            ]);
    }
}
