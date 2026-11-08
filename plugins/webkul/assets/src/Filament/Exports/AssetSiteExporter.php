<?php

namespace Webkul\Asset\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\Concerns\FormatsAssetExportFileName;
use Webkul\Asset\Models\AssetSite;

class AssetSiteExporter extends Exporter
{
    use FormatsAssetExportFileName;

    protected static ?string $model = AssetSite::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->when(auth()->check(), fn (Builder $q) => $q->whereIn('company_id', auth()->user()->allowedCompanies()->pluck('companies.id')->push(auth()->user()->default_company_id)->filter()->unique()->values()->all()))
            ->with(['parent', 'siteType', 'defaultCustodian', 'company', 'creator'])
            ->withCount(['assets as assets_count' => fn (Builder $q) => $q->where('status', AssetStatus::Held)])
            ->withSum(['assets as assets_sum_cost' => fn (Builder $q) => $q->where('status', AssetStatus::Held)], 'cost');
    }

    /**
     * @return array<int, mixed>
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('full_name')
                ->label(__('assets::filament/exports/asset-site.columns.full-name')),

            ExportColumn::make('parent.full_name')
                ->label(__('assets::filament/exports/asset-site.columns.parent'))
                ->enabledByDefault(false),

            ExportColumn::make('usage_type')
                ->label(__('assets::filament/exports/asset-site.columns.usage-type'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),

            ExportColumn::make('code')
                ->label(__('assets::filament/exports/asset-site.columns.code')),

            ExportColumn::make('siteType.name')
                ->label(__('assets::filament/exports/asset-site.columns.type')),

            ExportColumn::make('defaultCustodian.name')
                ->label(__('assets::filament/exports/asset-site.columns.default-custodian')),

            ExportColumn::make('is_active')
                ->label(__('assets::filament/exports/asset-site.columns.is-active')),

            ExportColumn::make('assets_count')
                ->label(__('assets::filament/exports/asset-site.columns.assets-count')),

            ExportColumn::make('assets_sum_cost')
                ->label(__('assets::filament/exports/asset-site.columns.total-cost')),

            ExportColumn::make('company.name')
                ->label(__('assets::filament/exports/asset-site.columns.company'))
                ->enabledByDefault(false),

            ExportColumn::make('creator.name')
                ->label(__('assets::filament/exports/asset-site.columns.creator'))
                ->enabledByDefault(false),

            ExportColumn::make('created_at')
                ->label(__('assets::filament/exports/asset-site.columns.created-at'))
                ->enabledByDefault(false),

            ExportColumn::make('updated_at')
                ->label(__('assets::filament/exports/asset-site.columns.updated-at'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('assets::filament/exports/asset-site.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('assets::filament/exports/asset-site.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
