<?php

namespace Webkul\Asset\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\Concerns\FormatsAssetExportFileName;
use Webkul\Asset\Models\AssetProduct;

class AssetProductExporter extends Exporter
{
    use FormatsAssetExportFileName;

    protected static ?string $model = AssetProduct::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->when(auth()->check(), fn (Builder $q) => $q->whereIn('company_id', auth()->user()->allowedCompanies()->pluck('companies.id')->push(auth()->user()->default_company_id)->filter()->unique()->values()->all()))
            ->with(['category', 'company'])
            ->withCount([
                'assets as terminated_assets_count' => fn (Builder $q) => $q->whereIn('status', [AssetStatus::Scrapped, AssetStatus::Lost]),
            ])
            ->withSum('assets as assets_count', 'initial_quantity')
            ->withSum(['assets as available_assets_count' => fn (Builder $q) => $q->where('status', AssetStatus::Available)], 'quantity')
            ->withSum(['assets as held_assets_count' => fn (Builder $q) => $q->where('status', AssetStatus::Held)], 'quantity')
            ->withSum('assets', 'cost');
    }

    /**
     * @return array<int, mixed>
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('assets::filament/exports/asset-product.columns.name')),

            ExportColumn::make('type')
                ->label(__('assets::filament/exports/asset-product.columns.type'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),

            ExportColumn::make('category.full_name')
                ->label(__('assets::filament/exports/asset-product.columns.category')),

            ExportColumn::make('model')
                ->label(__('assets::filament/exports/asset-product.columns.model')),

            ExportColumn::make('cost')
                ->label(__('assets::filament/exports/asset-product.columns.cost')),

            ExportColumn::make('assets_count')
                ->label(__('assets::filament/exports/asset-product.columns.assets-count')),

            ExportColumn::make('available_assets_count')
                ->label(__('assets::filament/exports/asset-product.columns.available-assets-count')),

            ExportColumn::make('held_assets_count')
                ->label(__('assets::filament/exports/asset-product.columns.held-assets-count')),

            ExportColumn::make('terminated_assets_count')
                ->label(__('assets::filament/exports/asset-product.columns.terminated-assets-count'))
                ->enabledByDefault(false),

            ExportColumn::make('assets_sum_cost')
                ->label(__('assets::filament/exports/asset-product.columns.total-cost')),

            ExportColumn::make('is_active')
                ->label(__('assets::filament/exports/asset-product.columns.is-active')),

            ExportColumn::make('company.name')
                ->label(__('assets::filament/exports/asset-product.columns.company'))
                ->enabledByDefault(false),

            ExportColumn::make('created_at')
                ->label(__('assets::filament/exports/asset-product.columns.created-at'))
                ->enabledByDefault(false),

            ExportColumn::make('updated_at')
                ->label(__('assets::filament/exports/asset-product.columns.updated-at'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('assets::filament/exports/asset-product.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('assets::filament/exports/asset-product.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
