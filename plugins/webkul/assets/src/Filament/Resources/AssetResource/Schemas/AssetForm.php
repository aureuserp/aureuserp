<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\Concerns\HasCustodyHolderFields;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Schemas\AssetSiteForm;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Schemas\AssetWarehouseForm;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Filament\Resources\ProductResource\Schemas\ProductForm;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Employee\Models\Employee;

class AssetForm
{
    use HasCustodyHolderFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset.form.sections.general.title'))
                            ->schema([
                                Hidden::make('selected_product_type')
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Hidden $component, ?Model $record): void {
                                        $component->state(
                                            $record instanceof Asset && $record->exists
                                                ? ($record->product?->type?->value ?? AssetProductType::Asset->value)
                                                : AssetProductType::Asset->value
                                        );
                                    }),

                                Select::make('product_id')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get, $state) => $query
                                            ->withTrashed()
                                            ->with('category')
                                            ->where(hide_deleted_unless_selected($state))
                                            ->where(fn (Builder $q) => $q->where('is_active', true)->when(filled($state), fn (Builder $sq) => $sq->orWhereKey($state)))
                                            ->where(owned_by_company($get('company_id'))),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (AssetProduct $record): string => $record->name
                                        .($record->model ? " ({$record->model})" : '')
                                        .($record->category ? " — {$record->category->full_name}" : '')
                                        .($record->trashed() ? ' (Deleted)' : ''))
                                    ->disableOptionWhen(fn ($label): bool => str_contains((string) $label, ' (Deleted)'))
                                    ->searchable(['name', 'model'])
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                                        if (! $state) {
                                            $set('selected_product_type', AssetProductType::Asset->value);

                                            return;
                                        }

                                        $product = AssetProduct::withTrashed()->find($state);

                                        $set('selected_product_type', $product?->type?->value ?? AssetProductType::Asset->value);

                                        if ($product && (blank($get('cost')) || (float) $get('cost') === 0.0) && filled($product->cost)) {
                                            $set('cost', $product->cost);
                                        }
                                    })
                                    ->createOptionForm(fn (Schema $schema): Schema => ProductForm::configure($schema))
                                    ->createOptionAction(fn ($action, Get $get) => $action
                                        ->visible(fn (): bool => ProductResource::canCreate())
                                        ->authorize(fn (): bool => ProductResource::canCreate())
                                        ->modalWidth('4xl')
                                        ->mutateDataUsing(fn (array $data): array => [
                                            ...$data,
                                            'company_id' => $data['company_id'] ?? $get('company_id') ?: current_company_id(),
                                            'is_active'  => $data['is_active'] ?? true,
                                        ]))
                                    ->editOptionForm(fn (Schema $schema): Schema => ProductForm::configure($schema))
                                    ->editOptionAction(fn ($action, Select $component) => $action
                                        ->visible(fn (): bool => filled($component->getState()) && (bool) auth()->user()?->can('update_asset_product'))
                                        ->authorize(fn (): bool => filled($component->getState()) && ($product = AssetProduct::find($component->getState())) && ProductResource::canEdit($product)))
                                    ->columnSpanFull(),

                                TextInput::make('asset_tag')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.asset-tag'))
                                    ->helperText(__('assets::filament/resources/asset.form.sections.general.fields.asset-tag-help'))
                                    ->placeholder(fn (): ?string => app(AssetSettings::class)->auto_generate_tag
                                        ? __('assets::filament/resources/asset.form.sections.general.fields.asset-tag-auto-placeholder')
                                        : null)
                                    ->required(fn (): bool => ! app(AssetSettings::class)->auto_generate_tag)
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, Get $get) => $rule->where('company_id', $get('company_id') ?: current_company_id()),
                                    )
                                    ->validationMessages([
                                        'unique' => __('assets::services/asset-custody.tag-already-exists'),
                                    ])
                                    ->maxLength(64),

                                TextInput::make('serial_no')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.serial-no'))
                                    ->required(fn (?Model $record, Get $get): bool => ! static::isConsumableState($record, $get) && (bool) app(AssetSettings::class)->require_serial_by_default)
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, Get $get) => $rule->where('company_id', $get('company_id') ?: current_company_id()),
                                    )
                                    ->validationMessages([
                                        'unique' => __('assets::services/asset-custody.serial-already-exists'),
                                    ])
                                    ->visible(fn (?Model $record, Get $get): bool => ! static::isConsumableState($record, $get))
                                    ->maxLength(255),

                                TextInput::make('initial_quantity')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.quantity'))
                                    ->helperText(__('assets::filament/resources/asset.form.sections.general.fields.quantity-help'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(fn (?Model $record, Get $get): bool => static::isConsumableState($record, $get))
                                    ->visible(fn (?Model $record, Get $get): bool => static::isConsumableState($record, $get))
                                    ->disabled(fn (?Model $record): bool => $record !== null && $record->exists),

                                Select::make('condition')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.condition'))
                                    ->options(AssetCondition::class)
                                    ->required(fn (?Model $record, Get $get): bool => ! static::isConsumableState($record, $get))
                                    ->visible(fn (?Model $record, Get $get): bool => ! static::isConsumableState($record, $get))
                                    ->default(fn (): mixed => AssetCondition::tryFrom((string) app(AssetSettings::class)->default_condition) ?? AssetCondition::New),
                            ])
                            ->columns(2),

                        Section::make(__('assets::filament/resources/asset.infolist.sections.acquisition.title'))
                            ->schema([
                                DatePicker::make('purchase_date')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.purchase-date'))
                                    ->native(false),

                                DatePicker::make('warranty_until')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.warranty-until'))
                                    ->native(false)
                                    ->visible(fn (?Model $record, Get $get): bool => ! static::isConsumableState($record, $get)),

                                TextInput::make('cost')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.cost'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),
                            ])
                            ->columns(3),

                        Section::make(__('assets::filament/resources/asset.infolist.sections.custody.title'))
                            ->visible(fn (?Model $record): bool => ! $record?->exists || $record->status === AssetStatus::Available)
                            ->schema([
                                Radio::make('initial_placement')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.initial-placement'))
                                    ->options(fn (): array => array_filter([
                                        'warehouse'        => __('assets::filament/resources/asset.form.sections.general.fields.placements.warehouse'),
                                        'site_asset'       => app(AssetSettings::class)->enable_site_assets ? __('assets::filament/resources/asset.form.sections.general.fields.placements.site-asset') : null,
                                        'employee_custody' => __('assets::filament/resources/asset.form.sections.general.fields.placements.employee-custody'),
                                    ]))
                                    ->default('warehouse')
                                    ->inline()
                                    ->live()
                                    ->dehydrated(false)
                                    ->visible(fn (?Model $record): bool => ! $record?->exists)
                                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                                        if ($state === 'warehouse') {
                                            $set('current_site_id', null);
                                            $set('current_employee_id', null);
                                            $set('issued_at', null);
                                            $set('expected_return_at', null);
                                            $set('custody_note', null);
                                            $set('initial_attachments', null);
                                        } elseif ($state === 'site_asset') {
                                            $set('warehouse_id', null);
                                            $set('current_employee_id', null);
                                            $set('expected_return_at', null);
                                            $set('issued_at', now()->toDateTimeString());

                                            $siteId = $get('current_site_id');
                                            if ($siteId && AssetSite::find($siteId)?->usage_type !== AssetSiteUsage::AssetLocation) {
                                                $set('current_site_id', null);
                                            }
                                        } elseif ($state === 'employee_custody') {
                                            $set('warehouse_id', null);
                                            $set('issued_at', now()->toDateTimeString());

                                            $duration = (int) (app(AssetSettings::class)->default_custody_duration_days ?? 0);
                                            if ($duration > 0 && blank($get('expected_return_at'))) {
                                                $set('expected_return_at', now()->addDays($duration)->toDateString());
                                            }

                                            $siteId = $get('current_site_id');
                                            if ($siteId && AssetSite::find($siteId)?->usage_type !== AssetSiteUsage::CustodyZone) {
                                                $set('current_site_id', null);
                                            }
                                        }
                                    })
                                    ->columnSpanFull(),

                                Select::make('warehouse_id')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.warehouse'))
                                    ->helperText(__('assets::filament/resources/asset.form.sections.general.fields.warehouse-help'))
                                    ->relationship(
                                        'warehouse',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->where('is_active', true)
                                            ->where(owned_by_company($get('company_id'))),
                                    )
                                    ->default(fn (Get $get): ?int => Asset::resolveDefaultWarehouseId($get('company_id') ?: current_company_id()))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (?Model $record, Get $get): bool => $record?->exists ? $record->status === AssetStatus::Available : ($get('initial_placement') ?? 'warehouse') === 'warehouse')
                                    ->required(fn (?Model $record, Get $get): bool => $record?->exists ? $record->status === AssetStatus::Available : ($get('initial_placement') ?? 'warehouse') === 'warehouse')
                                    ->createOptionForm(fn (Schema $schema): Schema => AssetWarehouseForm::configure($schema))
                                    ->createOptionAction(fn ($action) => $action
                                        ->visible(fn (): bool => AssetWarehouseResource::canCreate())
                                        ->authorize(fn (): bool => AssetWarehouseResource::canCreate()))
                                    ->columnSpanFull(),

                                Select::make('current_site_id')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.current-site'))
                                    ->relationship(
                                        'currentSite',
                                        'full_name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->where('is_active', true)
                                            ->where(owned_by_company($get('company_id')))
                                            ->when(
                                                $get('initial_placement') === 'site_asset',
                                                fn (Builder $q) => $q->where('usage_type', AssetSiteUsage::AssetLocation),
                                                fn (Builder $q) => $q->where('usage_type', AssetSiteUsage::CustodyZone),
                                            )
                                            ->orderBy('full_name'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->visible(fn (?Model $record, Get $get): bool => ! $record?->exists && in_array($get('initial_placement'), ['site_asset', 'employee_custody']))
                                    ->required(fn (?Model $record, Get $get): bool => ! $record?->exists && (
                                        $get('initial_placement') === 'site_asset'
                                        || ($get('initial_placement') === 'employee_custody' && (
                                            static::isConsumableState($record, $get)
                                                ? (bool) app(AssetSettings::class)->require_site_on_consumable_issue
                                                : (bool) app(AssetSettings::class)->require_site_on_custody
                                        ))
                                    ))
                                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                                        if (($get('initial_placement') ?? 'employee_custody') === 'employee_custody' && blank($get('current_employee_id'))) {
                                            $custodianId = static::siteDefaultCustodian($state ? (int) $state : null);
                                            if ($custodianId) {
                                                $set('current_employee_id', $custodianId);
                                            }
                                        }
                                    })
                                    ->createOptionForm(fn (Schema $schema, Get $get): Schema => AssetSiteForm::configure(
                                        $schema,
                                        $get('initial_placement') === 'site_asset' ? AssetSiteUsage::AssetLocation : AssetSiteUsage::CustodyZone,
                                    ))
                                    ->createOptionAction(fn ($action) => $action
                                        ->visible(fn (): bool => AssetSiteResource::canCreate())
                                        ->authorize(fn (): bool => AssetSiteResource::canCreate()))
                                    ->columnSpanFull(),

                                Select::make('current_employee_id')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.current-employee'))
                                    ->relationship(
                                        'currentEmployee',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->where('is_active', true)
                                            ->where(owned_by_company($get('company_id'))),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (?Model $record, Get $get): bool => ! $record?->exists && $get('initial_placement') === 'employee_custody')
                                    ->required(fn (?Model $record, Get $get): bool => ! $record?->exists && $get('initial_placement') === 'employee_custody')
                                    ->columnSpanFull(),

                                DateTimePicker::make('issued_at')
                                    ->label(__('assets::filament/resources/asset.actions.issue.fields.issued-at'))
                                    ->default(now())
                                    ->visible(fn (?Model $record, Get $get): bool => ! $record?->exists && in_array($get('initial_placement'), ['site_asset', 'employee_custody']))
                                    ->required(fn (?Model $record, Get $get): bool => ! $record?->exists && in_array($get('initial_placement'), ['site_asset', 'employee_custody'])),

                                DatePicker::make('expected_return_at')
                                    ->label(__('assets::filament/resources/asset.actions.issue.fields.expected-return-at'))
                                    ->afterOrEqual('issued_at')
                                    ->native(false)
                                    ->default(fn (): ?string => filled(app(AssetSettings::class)->default_custody_duration_days) && (int) app(AssetSettings::class)->default_custody_duration_days > 0
                                        ? now()->addDays((int) app(AssetSettings::class)->default_custody_duration_days)->toDateString()
                                        : null)
                                    ->required(fn (?Model $record, Get $get): bool => ! $record?->exists && ! static::isConsumableState($record, $get) && $get('initial_placement') === 'employee_custody' && (bool) app(AssetSettings::class)->require_expected_return_date)
                                    ->visible(fn (?Model $record, Get $get): bool => ! $record?->exists && ! static::isConsumableState($record, $get) && $get('initial_placement') === 'employee_custody'),

                                Textarea::make('custody_note')
                                    ->label(__('assets::filament/resources/asset.actions.issue.fields.issue-note'))
                                    ->rows(3)
                                    ->visible(fn (?Model $record, Get $get): bool => ! $record?->exists && in_array($get('initial_placement'), ['site_asset', 'employee_custody']))
                                    ->columnSpanFull(),

                                static::attachmentUploadField('initial_attachments', __('assets::filament/resources/asset.actions.issue.fields.issue-attachments'))
                                    ->visible(fn (?Model $record, Get $get): bool => ! $record?->exists && in_array($get('initial_placement'), ['site_asset', 'employee_custody']))
                                    ->required(fn (?Model $record, Get $get): bool => ! $record?->exists
                                        && in_array($get('initial_placement'), ['site_asset', 'employee_custody'])
                                        && (static::isConsumableState($record, $get)
                                            ? (bool) app(AssetSettings::class)->require_attachments_on_consumable_issue
                                            : (bool) app(AssetSettings::class)->require_attachments_on_custody)),
                            ]),

                        Section::make(__('assets::filament/resources/asset.infolist.sections.notes.title'))
                            ->schema([
                                Textarea::make('note')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.note'))
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset.form.sections.settings.title'))
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('assets::filament/resources/asset.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(fn (): ?int => current_company_id())
                                    ->disabled(fn (?Model $record): bool => static::isLockedCompany($record))
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, mixed $state) => clear_foreign_company_values($set, $get, [
                                        'product_id'          => AssetProduct::class,
                                        'warehouse_id'        => AssetWarehouse::class,
                                        'current_site_id'     => AssetSite::class,
                                        'current_employee_id' => Employee::class,
                                    ], $state)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    protected static function isConsumableState(?Model $record, Get $get): bool
    {
        $type = $get('selected_product_type');

        if (filled($type)) {
            return $type === AssetProductType::Consumable || $type === AssetProductType::Consumable->value;
        }

        return $record instanceof Asset
            && $record->relationLoaded('product')
            && (bool) $record->product?->isConsumable();
    }

    protected static function isLockedCompany(?Model $record): bool
    {
        return $record instanceof Asset
            && $record->exists
            && (
                $record->status === AssetStatus::Held
                || $record->movements()->where('type', '!=', MovementType::Registered)->exists()
            );
    }
}
