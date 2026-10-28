<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Actions\Concerns;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Schemas\AssetSiteForm;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Settings\AssetSettings;

trait HasCustodyHolderFields
{
    /**
     * @param  array<int, mixed>  $holderRules
     * @return array<int, mixed>
     */
    protected static function getHolderFields(string $translationPrefix, array $holderRules = []): array
    {
        return [
            Radio::make('allocation_mode')
                ->label(__("assets::filament/resources/asset.actions.{$translationPrefix}.fields.allocation-mode"))
                ->options(fn (): array => array_filter([
                    'employee_custody' => __("assets::filament/resources/asset.actions.{$translationPrefix}.fields.allocation-modes.employee-custody"),
                    'site_asset'       => app(AssetSettings::class)->enable_site_assets ? __("assets::filament/resources/asset.actions.{$translationPrefix}.fields.allocation-modes.site-asset") : null,
                ]))
                ->default(fn (?Asset $record): string => app(AssetSettings::class)->enable_site_assets && $record && $record->status === AssetStatus::Held && blank($record->current_employee_id) && filled($record->current_site_id) ? 'site_asset' : 'employee_custody')
                ->visible(fn (): bool => (bool) app(AssetSettings::class)->enable_site_assets)
                ->inline()
                ->live()
                ->columnSpanFull()
                ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                    if ($state === 'site_asset') {
                        $set('employee_id', null);

                        $siteId = $get('site_id');
                        if ($siteId && AssetSite::find($siteId)?->usage_type !== AssetSiteUsage::AssetLocation) {
                            $set('site_id', null);
                        }
                    } elseif ($state === 'employee_custody') {
                        $siteId = $get('site_id');
                        if ($siteId && AssetSite::find($siteId)?->usage_type !== AssetSiteUsage::CustodyZone) {
                            $set('site_id', null);
                        }
                    }
                }),

            Select::make('employee_id')
                ->label(__("assets::filament/resources/asset.actions.{$translationPrefix}.fields.employee"))
                ->relationship(
                    'currentEmployee',
                    'name',
                    modifyQueryUsing: fn (Builder $query, Asset $record) => $query->where('company_id', $record->company_id)->where('is_active', true),
                )
                ->searchable()
                ->preload()
                ->rules($holderRules)
                ->visible(fn (Get $get): bool => ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody')
                ->required(fn (Get $get): bool => ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody'),

            Select::make('site_id')
                ->label(__("assets::filament/resources/asset.actions.{$translationPrefix}.fields.site"))
                ->relationship(
                    'currentSite',
                    'full_name',
                    modifyQueryUsing: fn (Builder $query, Asset $record, Get $get) => $query
                        ->where('company_id', $record->company_id)
                        ->where('is_active', true)
                        ->when(
                            $get('allocation_mode') === 'site_asset',
                            fn (Builder $q) => $q->where('usage_type', AssetSiteUsage::AssetLocation),
                            fn (Builder $q) => $q->where('usage_type', AssetSiteUsage::CustodyZone),
                        )
                        ->orderBy('full_name'),
                )
                ->searchable()
                ->preload()
                ->rules($holderRules)
                ->live()
                ->required(fn (Get $get): bool => $get('allocation_mode') === 'site_asset' || ($get('allocation_mode') === 'employee_custody' && (bool) app(AssetSettings::class)->require_site_on_custody))
                ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                    if (($get('allocation_mode') ?? 'employee_custody') === 'employee_custody' && blank($get('employee_id'))) {
                        $custodianId = static::siteDefaultCustodian($state ? (int) $state : null);
                        if ($custodianId) {
                            $set('employee_id', $custodianId);
                        }
                    }
                })
                ->createOptionForm(fn (Schema $schema, Get $get): Schema => AssetSiteForm::configure(
                    $schema,
                    $get('allocation_mode') === 'site_asset' ? AssetSiteUsage::AssetLocation : AssetSiteUsage::CustodyZone,
                ))
                ->createOptionAction(fn ($action) => $action
                    ->visible(fn (): bool => AssetSiteResource::canCreate())
                    ->authorize(fn (): bool => AssetSiteResource::canCreate())),
        ];
    }

    public static function siteDefaultCustodian(?int $siteId): ?int
    {
        if (blank($siteId)) {
            return null;
        }

        return AssetSite::query()
            ->whereKey($siteId)
            ->value('default_custodian_employee_id');
    }

    public static function attachmentUploadField(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->multiple()
            ->maxFiles(5)
            ->maxSize(5120)
            ->acceptedFileTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'application/pdf',
            ])
            ->imageEditor()
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('1600')
            ->imageResizeTargetHeight('1200')
            ->directory('assets/attachments')
            ->disk('public')
            ->visibility('public')
            ->downloadable()
            ->openable()
            ->previewable()
            ->columnSpanFull();
    }
}
