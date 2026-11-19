<?php

namespace Webkul\Asset\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\Concerns\FormatsAssetExportFileName;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Employee\Models\Employee;

class AssetExporter extends Exporter
{
    use FormatsAssetExportFileName;

    protected static ?string $model = Asset::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->when(auth()->check(), fn (Builder $q) => $q->whereIn('company_id', auth()->user()->allowedCompanies()->pluck('companies.id')->push(auth()->user()->default_company_id)->filter()->unique()->values()->all()))
            ->with([
                'product.category',
                'warehouse',
                'currentEmployee.department',
                'currentSite.siteType',
                'company',
                'creator',
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    public static function getOptionsFormComponents(): array
    {
        return [
            Section::make(__('assets::filament/exports/asset.options.section'))
                ->schema([
                    Select::make('product_ids')
                        ->label(__('assets::filament/exports/asset.options.products'))
                        ->relationship('product', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->dehydrated()
                        ->loadStateFromRelationshipsUsing(null)
                        ->saveRelationshipsUsing(null)
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'product_id')),

                    Select::make('statuses')
                        ->label(__('assets::filament/exports/asset.options.statuses'))
                        ->options(AssetStatus::class)
                        ->multiple()
                        ->preload()
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'status')),

                    Select::make('conditions')
                        ->label(__('assets::filament/exports/asset.options.conditions'))
                        ->options(AssetCondition::class)
                        ->multiple()
                        ->preload()
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'condition')),

                    Select::make('category_ids')
                        ->label(__('assets::filament/exports/asset.options.categories'))
                        ->relationship('product.category', 'full_name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->dehydrated()
                        ->loadStateFromRelationshipsUsing(null)
                        ->saveRelationshipsUsing(null)
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'category_id')),

                    Select::make('warehouse_ids')
                        ->label(__('assets::filament/exports/asset.options.warehouses'))
                        ->relationship('warehouse', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->dehydrated()
                        ->loadStateFromRelationshipsUsing(null)
                        ->saveRelationshipsUsing(null)
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'warehouse_id')),

                    Select::make('site_ids')
                        ->label(__('assets::filament/exports/asset.options.sites'))
                        ->relationship('currentSite', 'full_name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->dehydrated()
                        ->loadStateFromRelationshipsUsing(null)
                        ->saveRelationshipsUsing(null)
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'current_site_id')),

                    Select::make('employee_ids')
                        ->label(__('assets::filament/exports/asset.options.employees'))
                        ->relationship('currentEmployee', 'name')
                        ->multiple()
                        ->searchable()
                        ->dehydrated()
                        ->loadStateFromRelationshipsUsing(null)
                        ->saveRelationshipsUsing(null)
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'current_employee_id')),
                ])
                ->columns(2),
        ];
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public static function applyExportOptions(Builder $query, array $options): Builder
    {
        return $query
            ->when(
                ! empty($options['product_ids']) && is_array($options['product_ids']),
                fn (Builder $q) => $q->whereIn('product_id', $options['product_ids']),
            )
            ->when(
                ! empty($options['statuses']) && is_array($options['statuses']),
                fn (Builder $q) => $q->whereIn('status', $options['statuses']),
            )
            ->when(
                ! empty($options['conditions']) && is_array($options['conditions']),
                fn (Builder $q) => $q->whereIn('condition', $options['conditions']),
            )
            ->when(
                ! empty($options['category_ids']) && is_array($options['category_ids']),
                fn (Builder $q) => $q->whereHas(
                    'product',
                    fn (Builder $productQuery) => AssetCategory::applySubtreeFilter($productQuery, $options['category_ids'], 'category_id'),
                ),
            )
            ->when(
                ! empty($options['warehouse_ids']) && is_array($options['warehouse_ids']),
                fn (Builder $q) => $q->whereIn('warehouse_id', $options['warehouse_ids']),
            )
            ->when(
                ! empty($options['site_ids']) && is_array($options['site_ids']),
                fn (Builder $q) => AssetSite::applySubtreeFilter($q, $options['site_ids'], 'current_site_id'),
            )
            ->when(
                ! empty($options['employee_ids']) && is_array($options['employee_ids']),
                fn (Builder $q) => $q->whereIn('current_employee_id', $options['employee_ids']),
            );
    }

    /**
     * @return array<int, string|null>
     */
    protected static function fileNameSegments(Export $export): array
    {
        $options = $export->getOptions();

        return array_merge(
            filled($options['context'] ?? null) ? [(string) $options['context']] : [],
            static::nameOrCountSegments(
                __('assets::filament/exports/asset.options.statuses'),
                static::optionEnumLabels(AssetStatus::class, $options['statuses'] ?? null),
            ),
            static::nameOrCountSegments(
                __('assets::filament/exports/asset.options.conditions'),
                static::optionEnumLabels(AssetCondition::class, $options['conditions'] ?? null),
            ),
            static::nameOrCountSegments(
                __('assets::filament/exports/asset.options.products'),
                static::optionRecordNames(AssetProduct::class, $options['product_ids'] ?? null, 'name'),
            ),
            static::nameOrCountSegments(
                __('assets::filament/exports/asset.options.categories'),
                static::optionRecordNames(AssetCategory::class, $options['category_ids'] ?? null, 'full_name'),
            ),
            static::nameOrCountSegments(
                __('assets::filament/exports/asset.options.warehouses'),
                static::optionRecordNames(AssetWarehouse::class, $options['warehouse_ids'] ?? null, 'name'),
            ),
            static::nameOrCountSegments(
                __('assets::filament/exports/asset.options.sites'),
                static::optionRecordNames(AssetSite::class, $options['site_ids'] ?? null, 'full_name'),
            ),
            static::nameOrCountSegments(
                __('assets::filament/exports/asset.options.employees'),
                static::optionRecordNames(Employee::class, $options['employee_ids'] ?? null, 'name'),
            ),
        );
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('asset_tag')
                ->label(__('assets::filament/exports/asset.columns.asset-tag')),

            ExportColumn::make('product.name')
                ->label(__('assets::filament/exports/asset.columns.product')),

            ExportColumn::make('product.model')
                ->label(__('assets::filament/exports/asset.columns.model')),

            ExportColumn::make('product.type')
                ->label(__('assets::filament/exports/asset.columns.product-type'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),

            ExportColumn::make('initial_quantity')
                ->label(__('assets::filament/exports/asset.columns.initial-quantity')),

            ExportColumn::make('quantity')
                ->label(__('assets::filament/exports/asset.columns.quantity')),

            ExportColumn::make('product.category.full_name')
                ->label(__('assets::filament/exports/asset.columns.category')),

            ExportColumn::make('serial_no')
                ->label(__('assets::filament/exports/asset.columns.serial-no')),

            ExportColumn::make('status')
                ->label(__('assets::filament/exports/asset.columns.status'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),

            ExportColumn::make('condition')
                ->label(__('assets::filament/exports/asset.columns.condition'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),

            ExportColumn::make('warehouse.name')
                ->label(__('assets::filament/exports/asset.columns.warehouse')),

            ExportColumn::make('currentEmployee.name')
                ->label(__('assets::filament/exports/asset.columns.current-employee')),

            ExportColumn::make('currentEmployee.department.name')
                ->label(__('assets::filament/exports/asset.columns.department')),

            ExportColumn::make('currentSite.full_name')
                ->label(__('assets::filament/exports/asset.columns.current-site')),

            ExportColumn::make('currentSite.siteType.name')
                ->label(__('assets::filament/exports/asset.columns.site-type'))
                ->enabledByDefault(false),

            ExportColumn::make('issued_at')
                ->label(__('assets::filament/exports/asset.columns.issued-at')),

            ExportColumn::make('expected_return_at')
                ->label(__('assets::filament/exports/asset.columns.expected-return-at')),

            ExportColumn::make('cost')
                ->label(__('assets::filament/exports/asset.columns.cost')),

            ExportColumn::make('purchase_date')
                ->label(__('assets::filament/exports/asset.columns.purchase-date'))
                ->enabledByDefault(false),

            ExportColumn::make('warranty_until')
                ->label(__('assets::filament/exports/asset.columns.warranty-until'))
                ->enabledByDefault(false),

            ExportColumn::make('custody_note')
                ->label(__('assets::filament/exports/asset.columns.custody-note'))
                ->enabledByDefault(false),

            ExportColumn::make('note')
                ->label(__('assets::filament/exports/asset.columns.note'))
                ->enabledByDefault(false),

            ExportColumn::make('company.name')
                ->label(__('assets::filament/exports/asset.columns.company'))
                ->enabledByDefault(false),

            ExportColumn::make('created_at')
                ->label(__('assets::filament/exports/asset.columns.created-at'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('assets::filament/exports/asset.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('assets::filament/exports/asset.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
