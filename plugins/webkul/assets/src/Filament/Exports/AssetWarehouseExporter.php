<?php

namespace Webkul\Asset\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\Concerns\FormatsAssetExportFileName;
use Webkul\Asset\Models\AssetWarehouse;

class AssetWarehouseExporter extends Exporter
{
    use FormatsAssetExportFileName;

    protected static ?string $model = AssetWarehouse::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->when(auth()->check(), fn (Builder $q) => $q->whereIn('company_id', auth()->user()->allowedCompanies()->pluck('companies.id')->push(auth()->user()->default_company_id)->filter()->unique()->values()->all()))
            ->with(['custodian', 'company', 'creator'])
            ->withCount(['assets as assets_count' => fn (Builder $q) => $q->where('status', AssetStatus::Available)])
            ->withSum(['assets as assets_sum_cost' => fn (Builder $q) => $q->where('status', AssetStatus::Available)], 'cost');
    }

    /**
     * @return array<int, mixed>
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('assets::filament/exports/asset-warehouse.columns.name')),

            ExportColumn::make('code')
                ->label(__('assets::filament/exports/asset-warehouse.columns.code')),

            ExportColumn::make('custodian.name')
                ->label(__('assets::filament/exports/asset-warehouse.columns.custodian')),

            ExportColumn::make('assets_count')
                ->label(__('assets::filament/exports/asset-warehouse.columns.assets-count')),

            ExportColumn::make('assets_sum_cost')
                ->label(__('assets::filament/exports/asset-warehouse.columns.total-cost')),

            ExportColumn::make('is_active')
                ->label(__('assets::filament/exports/asset-warehouse.columns.is-active')),

            ExportColumn::make('company.name')
                ->label(__('assets::filament/exports/asset-warehouse.columns.company'))
                ->enabledByDefault(false),

            ExportColumn::make('creator.name')
                ->label(__('assets::filament/exports/asset-warehouse.columns.creator'))
                ->enabledByDefault(false),

            ExportColumn::make('created_at')
                ->label(__('assets::filament/exports/asset-warehouse.columns.created-at'))
                ->enabledByDefault(false),

            ExportColumn::make('updated_at')
                ->label(__('assets::filament/exports/asset-warehouse.columns.updated-at'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('assets::filament/exports/asset-warehouse.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('assets::filament/exports/asset-warehouse.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
