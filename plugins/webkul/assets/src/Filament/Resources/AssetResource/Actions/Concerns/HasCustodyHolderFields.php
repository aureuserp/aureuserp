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
use Webkul\Employee\Models\Employee;

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
                        $set('employee_ids', null);
                        $set('department_ids', null);

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
                ->required(fn (Get $get): bool => $get('allocation_mode') === 'site_asset'
                    || (($get('allocation_mode') ?? 'employee_custody') === 'employee_custody' && (
                        (bool) $get('selected_consumable')
                            ? (bool) app(AssetSettings::class)->require_site_on_consumable_issue
                            : (bool) app(AssetSettings::class)->require_site_on_custody
                    )))
                ->afterStateUpdated(function (Set $set, Get $get, mixed $state, Asset $record): void {
                    if (($get('allocation_mode') ?? 'employee_custody') !== 'employee_custody') {
                        return;
                    }

                    if (static::isBatchConsumableMode($get)) {
                        static::syncConsumableRecipients($set, $get, (int) $record->company_id);

                        return;
                    }

                    if (blank($get('employee_id'))) {
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

            Select::make('department_ids')
                ->label(__("assets::filament/resources/asset.actions.{$translationPrefix}.fields.departments"))
                ->helperText(__("assets::filament/resources/asset.actions.{$translationPrefix}.fields.departments-help"))
                ->relationship(
                    'currentEmployee.department',
                    'name',
                    modifyQueryUsing: fn (Builder $query, Asset $record) => $query->where('company_id', $record->company_id)->orderBy('name'),
                )
                ->multiple()
                ->searchable()
                ->preload()
                ->live()
                ->dehydrated(false)
                ->loadStateFromRelationshipsUsing(null)
                ->saveRelationshipsUsing(null)
                ->visible(fn (Get $get): bool => static::isBatchConsumableMode($get) && ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody')
                ->afterStateUpdated(fn (Set $set, Get $get, Asset $record) => static::syncConsumableRecipients($set, $get, (int) $record->company_id)),

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
                ->visible(fn (Get $get): bool => ! static::isBatchConsumableMode($get) && ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody')
                ->required(fn (Get $get): bool => ! static::isBatchConsumableMode($get) && ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody'),

            Select::make('employee_ids')
                ->label(__("assets::filament/resources/asset.actions.{$translationPrefix}.fields.employees"))
                ->relationship(
                    'currentEmployee',
                    'name',
                    modifyQueryUsing: fn (Builder $query, Asset $record) => $query->where('company_id', $record->company_id)->where('is_active', true)->orderBy('name'),
                )
                ->multiple()
                ->searchable()
                ->preload()
                ->live()
                ->rules($holderRules)
                ->dehydrated()
                ->loadStateFromRelationshipsUsing(null)
                ->saveRelationshipsUsing(null)
                ->extraFieldWrapperAttributes(['class' => 'fi-fo-select-scrollable'])
                ->extraAttributes(['class' => 'fi-fo-select-scrollable'])
                ->hint(function (Get $get) use ($translationPrefix): string {
                    $count = count(array_filter((array) ($get('employee_ids') ?? [])));
                    $quantity = max(0, (float) ($get('quantity') ?: 1));
                    $total = round($count * $quantity, 2);

                    return __("assets::filament/resources/asset.actions.{$translationPrefix}.fields.recipients-summary", [
                        'count' => $count,
                        'total' => $total,
                    ]);
                })
                ->visible(fn (Get $get): bool => static::isBatchConsumableMode($get) && ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody')
                ->required(fn (Get $get): bool => static::isBatchConsumableMode($get) && ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody'),
        ];
    }

    protected static function isBatchConsumableMode(Get $get): bool
    {
        return (bool) $get('selected_consumable')
            && (bool) app(AssetSettings::class)->enable_consumables_batch_issue;
    }

    /**
     * @param  array<int, int|string>  $departmentIds
     * @return array<int, int>
     */
    public static function departmentActiveEmployeeIds(array $departmentIds, int $companyId): array
    {
        $ids = array_values(array_filter(array_map('intval', $departmentIds)));

        if ($ids === []) {
            return [];
        }

        return Employee::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereIn('department_id', $ids)
            ->orderBy('name')
            ->pluck('id')
            ->all();
    }

    protected static function syncConsumableRecipients(Set $set, Get $get, int $companyId): void
    {
        if (! static::isBatchConsumableMode($get) || ($get('allocation_mode') ?? 'employee_custody') !== 'employee_custody') {
            return;
        }

        $siteId = $get('site_id') ? (int) $get('site_id') : null;
        $departmentIds = (array) ($get('department_ids') ?? []);

        $fromSite = ($siteId && (bool) app(AssetSettings::class)->auto_fill_consumable_employees_by_site)
            ? static::siteActiveEmployeeIds($siteId, $companyId)
            : [];

        $fromDepartments = static::departmentActiveEmployeeIds($departmentIds, $companyId);

        $merged = array_values(array_unique(array_merge($fromSite, $fromDepartments)));

        if ($merged !== [] || $departmentIds !== [] || $siteId !== null) {
            $set('employee_ids', $merged);
        }
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

    /**
     * @return array<int, int>
     */
    public static function siteActiveEmployeeIds(?int $siteId, int $companyId): array
    {
        if (blank($siteId)) {
            return [];
        }

        $custodianId = static::siteDefaultCustodian($siteId);

        $holderIds = Asset::query()
            ->where('company_id', $companyId)
            ->where('current_site_id', $siteId)
            ->where('status', AssetStatus::Held)
            ->whereNotNull('current_employee_id')
            ->pluck('current_employee_id');

        return Employee::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereIn('id', $holderIds->push($custodianId)->filter()->unique()->values()->all())
            ->pluck('id')
            ->all();
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
