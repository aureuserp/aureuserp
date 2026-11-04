<?php

namespace Webkul\Asset\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Filament\Exports\Concerns\FormatsAssetExportFileName;
use Webkul\Asset\Models\AssetCategory;

class AssetCategoryExporter extends Exporter
{
    use FormatsAssetExportFileName;

    protected static ?string $model = AssetCategory::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->when(
                auth()->check(),
                fn (Builder $q) => $q->where(function (Builder $companyQuery): void {
                    $companyQuery
                        ->whereIn('company_id', auth()->user()->allowedCompanies()->pluck('companies.id')->push(auth()->user()->default_company_id)->filter()->unique()->values()->all())
                        ->orWhereNull('company_id');
                }),
            )
            ->with(['parent', 'company', 'creator'])
            ->withCount(['products', 'assets'])
            ->withSum('assets', 'cost');
    }

    /**
     * @return array<int, mixed>
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('assets::filament/exports/asset-category.columns.name')),

            ExportColumn::make('full_name')
                ->label(__('assets::filament/exports/asset-category.columns.full-name')),

            ExportColumn::make('parent.full_name')
                ->label(__('assets::filament/exports/asset-category.columns.parent'))
                ->enabledByDefault(false),

            ExportColumn::make('products_count')
                ->label(__('assets::filament/exports/asset-category.columns.products-count')),

            ExportColumn::make('assets_count')
                ->label(__('assets::filament/exports/asset-category.columns.assets-count')),

            ExportColumn::make('assets_sum_cost')
                ->label(__('assets::filament/exports/asset-category.columns.total-cost')),

            ExportColumn::make('company.name')
                ->label(__('assets::filament/exports/asset-category.columns.company'))
                ->enabledByDefault(false),

            ExportColumn::make('creator.name')
                ->label(__('assets::filament/exports/asset-category.columns.creator'))
                ->enabledByDefault(false),

            ExportColumn::make('created_at')
                ->label(__('assets::filament/exports/asset-category.columns.created-at'))
                ->enabledByDefault(false),

            ExportColumn::make('updated_at')
                ->label(__('assets::filament/exports/asset-category.columns.updated-at'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('assets::filament/exports/asset-category.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('assets::filament/exports/asset-category.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
