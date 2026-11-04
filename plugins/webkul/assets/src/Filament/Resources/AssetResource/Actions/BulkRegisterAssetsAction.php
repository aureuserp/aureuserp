<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Filament\Resources\AssetResource;
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
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class BulkRegisterAssetsAction
{
    use HasCustodyHolderFields;

    public static function make(?string $name = 'bulkRegister'): Action
    {
        return Action::make($name)
            ->label(__('assets::filament/resources/asset.actions.bulk-register.label'))
            ->icon('heroicon-o-queue-list')
            ->color('primary')
            ->visible(fn (): bool => AssetResource::canCreate())
            ->authorize(fn (): bool => AssetResource::canCreate())
            ->modalWidth('4xl')
            ->modalHeading(__('assets::filament/resources/asset.actions.bulk-register.modal.heading'))
            ->modalDescription(__('assets::filament/resources/asset.actions.bulk-register.modal.description'))
            ->modalSubmitActionLabel(__('assets::filament/resources/asset.actions.bulk-register.modal.submit'))
            ->schema([
                Section::make(__('assets::filament/resources/asset.actions.bulk-register.sections.shared'))
                    ->schema([
                        Select::make('company_id')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn (): ?int => current_company_id())
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, mixed $state) => clear_foreign_company_values($set, $get, [
                                'product_id'   => AssetProduct::class,
                                'warehouse_id' => AssetWarehouse::class,
                                'site_id'      => AssetSite::class,
                                'employee_id'  => Employee::class,
                            ], $state)),

                        Select::make('product_id')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.product'))
                            ->relationship(
                                'product',
                                'name',
                                modifyQueryUsing: fn (Builder $query, Get $get, $state) => $query
                                    ->withTrashed()
                                    ->with('category')
                                    ->where(hide_deleted_unless_selected($state))
                                    ->where(fn (Builder $q) => $q->where('is_active', true)->when(filled($state), fn (Builder $sq) => $sq->orWhereKey($state)))
                                    ->where('type', AssetProductType::Asset->value)
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
                                    return;
                                }

                                $product = AssetProduct::withTrashed()->find($state);

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
                                ->authorize(fn (): bool => filled($component->getState()) && ($product = AssetProduct::find($component->getState())) && ProductResource::canEdit($product))),

                        Select::make('condition')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.condition'))
                            ->options(AssetCondition::class)
                            ->required()
                            ->default(fn (): mixed => AssetCondition::tryFrom((string) app(AssetSettings::class)->default_condition) ?? AssetCondition::New),

                        TextInput::make('cost')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.cost'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        DatePicker::make('purchase_date')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.purchase-date'))
                            ->native(false),

                        DatePicker::make('warranty_until')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.warranty-until'))
                            ->native(false),

                        Radio::make('initial_placement')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.initial-placement'))
                            ->options(fn (): array => array_filter([
                                'warehouse'        => __('assets::filament/resources/asset.actions.bulk-register.fields.placements.warehouse'),
                                'site_asset'       => app(AssetSettings::class)->enable_site_assets ? __('assets::filament/resources/asset.actions.bulk-register.fields.placements.site-asset') : null,
                                'employee_custody' => __('assets::filament/resources/asset.actions.bulk-register.fields.placements.employee-custody'),
                            ]))
                            ->default('warehouse')
                            ->inline()
                            ->live()
                            ->columnSpanFull()
                            ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                                if ($state === 'warehouse') {
                                    $set('site_id', null);
                                    $set('employee_id', null);
                                } elseif ($state === 'site_asset') {
                                    $set('warehouse_id', null);
                                    $set('employee_id', null);

                                    $siteId = $get('site_id');
                                    if ($siteId && AssetSite::find($siteId)?->usage_type !== AssetSiteUsage::AssetLocation) {
                                        $set('site_id', null);
                                    }
                                } elseif ($state === 'employee_custody') {
                                    $set('warehouse_id', null);

                                    $duration = (int) (app(AssetSettings::class)->default_custody_duration_days ?? 0);
                                    if ($duration > 0 && blank($get('expected_return_at'))) {
                                        $set('expected_return_at', now()->addDays($duration)->toDateString());
                                    }

                                    $siteId = $get('site_id');
                                    if ($siteId && AssetSite::find($siteId)?->usage_type !== AssetSiteUsage::CustodyZone) {
                                        $set('site_id', null);
                                    }
                                }
                            }),

                        Select::make('warehouse_id')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.warehouse'))
                            ->helperText(__('assets::filament/resources/asset.actions.bulk-register.fields.warehouse-help'))
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
                            ->visible(fn (Get $get): bool => ($get('initial_placement') ?? 'warehouse') === 'warehouse')
                            ->required(fn (Get $get): bool => ($get('initial_placement') ?? 'warehouse') === 'warehouse')
                            ->createOptionForm(fn (Schema $schema): Schema => AssetWarehouseForm::configure($schema))
                            ->createOptionAction(fn ($action) => $action
                                ->visible(fn (): bool => AssetWarehouseResource::canCreate())
                                ->authorize(fn (): bool => AssetWarehouseResource::canCreate()))
                            ->columnSpanFull(),

                        Select::make('site_id')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.site'))
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
                            ->visible(fn (Get $get): bool => in_array($get('initial_placement'), ['site_asset', 'employee_custody']))
                            ->required(fn (Get $get): bool => $get('initial_placement') === 'site_asset' || ($get('initial_placement') === 'employee_custody' && (bool) app(AssetSettings::class)->require_site_on_custody))
                            ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                                if (($get('initial_placement') ?? 'employee_custody') === 'employee_custody' && blank($get('employee_id'))) {
                                    $custodianId = static::siteDefaultCustodian($state ? (int) $state : null);
                                    if ($custodianId) {
                                        $set('employee_id', $custodianId);
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

                        Select::make('employee_id')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.employee'))
                            ->relationship(
                                'currentEmployee',
                                'name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                    ->where('is_active', true)
                                    ->where(owned_by_company($get('company_id'))),
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('initial_placement') === 'employee_custody')
                            ->required(fn (Get $get): bool => $get('initial_placement') === 'employee_custody')
                            ->columnSpanFull(),

                        DatePicker::make('expected_return_at')
                            ->label(__('assets::filament/resources/asset.actions.issue.fields.expected-return-at'))
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('initial_placement') === 'employee_custody')
                            ->required(fn (Get $get): bool => $get('initial_placement') === 'employee_custody' && (bool) app(AssetSettings::class)->require_expected_return_date)
                            ->default(fn (): ?string => filled(app(AssetSettings::class)->default_custody_duration_days) && (int) app(AssetSettings::class)->default_custody_duration_days > 0
                                ? now()->addDays((int) app(AssetSettings::class)->default_custody_duration_days)->toDateString()
                                : null)
                            ->columnSpanFull(),

                        static::attachmentUploadField('attachments', __('assets::filament/resources/asset.actions.bulk-register.fields.attachments'))
                            ->visible(fn (Get $get): bool => in_array($get('initial_placement'), ['site_asset', 'employee_custody'], true))
                            ->required(fn (Get $get): bool => in_array($get('initial_placement'), ['site_asset', 'employee_custody'], true) && (bool) app(AssetSettings::class)->require_attachments_on_custody),

                        Textarea::make('note')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.note'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make(__('assets::filament/resources/asset.actions.bulk-register.sections.mode'))
                    ->schema([
                        Radio::make('tracking_mode')
                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.tracking-mode'))
                            ->options(fn (): array => array_filter([
                                'with_serials'    => __('assets::filament/resources/asset.actions.bulk-register.fields.modes.with-serials'),
                                'without_serials' => app(AssetSettings::class)->require_serial_by_default ? null : __('assets::filament/resources/asset.actions.bulk-register.fields.modes.without-serials'),
                            ]))
                            ->default('with_serials')
                            ->live()
                            ->inline(),

                        Group::make()
                            ->schema([
                                Repeater::make('items')
                                    ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.items'))
                                    ->compact()
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->maxItems(200)
                                    ->reorderable(false)
                                    ->addable()
                                    ->deletable()
                                    ->table([
                                        TableColumn::make('serial_no')
                                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.serial-no')),
                                        TableColumn::make('asset_tag')
                                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.asset-tag')),
                                        TableColumn::make('condition')
                                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.condition')),
                                        TableColumn::make('note')
                                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.unit-note')),
                                    ])
                                    ->schema([
                                        TextInput::make('serial_no')
                                            ->required()
                                            ->distinct(),
                                        TextInput::make('asset_tag')
                                            ->placeholder(fn (): ?string => app(AssetSettings::class)->auto_generate_tag
                                                ? __('assets::filament/resources/asset.form.sections.general.fields.asset-tag-auto-placeholder')
                                                : null)
                                            ->required(fn (): bool => ! app(AssetSettings::class)->auto_generate_tag)
                                            ->distinct(),
                                        Select::make('condition')
                                            ->options(AssetCondition::class)
                                            ->default(fn (Get $get) => $get('../../condition') ?? AssetCondition::New),
                                        TextInput::make('note'),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn (Get $get): bool => ($get('tracking_mode') ?? 'with_serials') === 'with_serials'),

                        Group::make()
                            ->schema([
                                TextInput::make('quantity')
                                    ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.quantity'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(200)
                                    ->default(1)
                                    ->required()
                                    ->visible(fn (): bool => (bool) app(AssetSettings::class)->auto_generate_tag),

                                Repeater::make('items')
                                    ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.items'))
                                    ->compact()
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->maxItems(200)
                                    ->reorderable(false)
                                    ->addable()
                                    ->deletable()
                                    ->table([
                                        TableColumn::make('asset_tag')
                                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.asset-tag')),
                                        TableColumn::make('condition')
                                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.condition')),
                                        TableColumn::make('note')
                                            ->label(__('assets::filament/resources/asset.actions.bulk-register.fields.unit-note')),
                                    ])
                                    ->schema([
                                        TextInput::make('asset_tag')
                                            ->required()
                                            ->distinct(),
                                        Select::make('condition')
                                            ->options(AssetCondition::class)
                                            ->default(fn (Get $get) => $get('../../condition') ?? AssetCondition::New),
                                        TextInput::make('note'),
                                    ])
                                    ->columnSpanFull()
                                    ->visible(fn (): bool => ! (bool) app(AssetSettings::class)->auto_generate_tag),
                            ])
                            ->visible(fn (Get $get): bool => ($get('tracking_mode') ?? 'with_serials') === 'without_serials'),
                    ]),
            ])
            ->action(function (array $data): void {
                try {
                    $created = app(AssetCustodyService::class)->bulkRegister($data);
                } catch (ValidationException $e) {
                    Notification::make()
                        ->danger()
                        ->title(collect($e->errors())->flatten()->first())
                        ->send();

                    throw $e;
                }

                Notification::make()
                    ->success()
                    ->title(__('assets::filament/resources/asset.actions.bulk-register.notification.title'))
                    ->body(__('assets::filament/resources/asset.actions.bulk-register.notification.body', ['count' => $created->count()]))
                    ->send();
            });
    }
}
