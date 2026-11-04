<?php

namespace Webkul\Asset\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Filament\Exports\Concerns\FormatsAssetExportFileName;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetSite;
use Webkul\Employee\Models\Employee;

class AssetMovementExporter extends Exporter
{
    use FormatsAssetExportFileName;

    protected static ?string $model = AssetMovement::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->when(auth()->check(), fn (Builder $q) => $q->whereIn('company_id', auth()->user()->allowedCompanies()->pluck('companies.id')->push(auth()->user()->default_company_id)->filter()->unique()->values()->all()))
            ->with([
                'asset.product.category',
                'fromWarehouse',
                'fromEmployee.department',
                'fromSite',
                'toWarehouse',
                'toEmployee.department',
                'toSite',
                'performedBy',
                'company',
            ]);
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Section::make(__('assets::filament/exports/asset-movement.options.section'))
                ->schema([
                    DatePicker::make('date_from')
                        ->label(__('assets::filament/exports/asset-movement.options.date-from'))
                        ->native(false)
                        ->default(fn (mixed $livewire): mixed => static::tableFilterScalar($livewire, 'occurred_at', 'from')),

                    DatePicker::make('date_until')
                        ->label(__('assets::filament/exports/asset-movement.options.date-until'))
                        ->native(false)
                        ->default(fn (mixed $livewire): mixed => static::tableFilterScalar($livewire, 'occurred_at', 'until')),

                    Select::make('types')
                        ->label(__('assets::filament/exports/asset-movement.options.types'))
                        ->options(MovementType::class)
                        ->multiple()
                        ->preload()
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'type')),

                    Select::make('site_ids')
                        ->label(__('assets::filament/exports/asset-movement.options.sites'))
                        ->relationship('toSite', 'full_name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->dehydrated()
                        ->loadStateFromRelationshipsUsing(null)
                        ->saveRelationshipsUsing(null)
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'site_id')),

                    Select::make('employee_ids')
                        ->label(__('assets::filament/exports/asset-movement.options.employees'))
                        ->relationship('toEmployee', 'name')
                        ->multiple()
                        ->searchable()
                        ->dehydrated()
                        ->loadStateFromRelationshipsUsing(null)
                        ->saveRelationshipsUsing(null)
                        ->default(fn (mixed $livewire): array => static::tableFilterValues($livewire, 'employee_id'))
                        ->columnSpanFull(),
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
                filled($options['date_from'] ?? null),
                fn (Builder $q) => $q->whereDate('occurred_at', '>=', $options['date_from']),
            )
            ->when(
                filled($options['date_until'] ?? null),
                fn (Builder $q) => $q->whereDate('occurred_at', '<=', $options['date_until']),
            )
            ->when(
                ! empty($options['types']) && is_array($options['types']),
                fn (Builder $q) => $q->whereIn('type', $options['types']),
            )
            ->when(
                ! empty($options['site_ids']) && is_array($options['site_ids']),
                function (Builder $q) use ($options): Builder {
                    $subtreeIds = AssetSite::subtreeIds($options['site_ids']);

                    return $subtreeIds === []
                        ? $q
                        : $q->where(fn (Builder $sq) => $sq
                            ->whereIn('to_site_id', $subtreeIds)
                            ->orWhereIn('from_site_id', $subtreeIds));
                },
            )
            ->when(
                ! empty($options['employee_ids']) && is_array($options['employee_ids']),
                fn (Builder $q) => $q->where(fn (Builder $sq) => $sq
                    ->whereIn('to_employee_id', $options['employee_ids'])
                    ->orWhereIn('from_employee_id', $options['employee_ids'])),
            );
    }

    /**
     * @return array<int, string|null>
     */
    protected static function fileNameSegments(Export $export): array
    {
        $options = $export->getOptions();

        $dateFrom = static::dateSegmentValue($options['date_from'] ?? null);
        $dateUntil = static::dateSegmentValue($options['date_until'] ?? null);

        $dateSegment = match (true) {
            filled($dateFrom) && filled($dateUntil) => $dateFrom.'_to_'.$dateUntil,
            filled($dateFrom)                       => 'from_'.$dateFrom,
            filled($dateUntil)                      => 'until_'.$dateUntil,
            default                                 => null,
        };

        return array_merge(
            filled($options['context'] ?? null) ? [(string) $options['context']] : [],
            filled($dateSegment) ? [$dateSegment] : [],
            static::nameOrCountSegments(
                __('assets::filament/exports/asset-movement.options.types'),
                static::optionEnumLabels(MovementType::class, $options['types'] ?? null),
            ),
            static::nameOrCountSegments(
                __('assets::filament/exports/asset-movement.options.sites'),
                static::optionRecordNames(AssetSite::class, $options['site_ids'] ?? null, 'full_name'),
            ),
            static::nameOrCountSegments(
                __('assets::filament/exports/asset-movement.options.employees'),
                static::optionRecordNames(Employee::class, $options['employee_ids'] ?? null, 'name'),
            ),
        );
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('occurred_at')
                ->label(__('assets::filament/exports/asset-movement.columns.occurred-at')),

            ExportColumn::make('asset.asset_tag')
                ->label(__('assets::filament/exports/asset-movement.columns.asset-tag')),

            ExportColumn::make('asset.product.name')
                ->label(__('assets::filament/exports/asset-movement.columns.product')),

            ExportColumn::make('asset.product.model')
                ->label(__('assets::filament/exports/asset-movement.columns.model'))
                ->enabledByDefault(false),

            ExportColumn::make('asset.product.category.full_name')
                ->label(__('assets::filament/exports/asset-movement.columns.category')),

            ExportColumn::make('asset.serial_no')
                ->label(__('assets::filament/exports/asset-movement.columns.serial-no')),

            ExportColumn::make('type')
                ->label(__('assets::filament/exports/asset-movement.columns.type'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),

            ExportColumn::make('quantity')
                ->label(__('assets::filament/exports/asset-movement.columns.quantity')),

            ExportColumn::make('source_type')
                ->label(__('assets::filament/exports/asset-movement.columns.source-type'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),

            ExportColumn::make('from_display')
                ->label(__('assets::filament/exports/asset-movement.columns.from')),

            ExportColumn::make('to_display')
                ->label(__('assets::filament/exports/asset-movement.columns.to')),

            ExportColumn::make('condition')
                ->label(__('assets::filament/exports/asset-movement.columns.condition'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),

            ExportColumn::make('expected_return_at')
                ->label(__('assets::filament/exports/asset-movement.columns.expected-return-at')),

            ExportColumn::make('performedBy.name')
                ->label(__('assets::filament/exports/asset-movement.columns.performed-by')),

            ExportColumn::make('note')
                ->label(__('assets::filament/exports/asset-movement.columns.note')),

            ExportColumn::make('company.name')
                ->label(__('assets::filament/exports/asset-movement.columns.company'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('assets::filament/exports/asset-movement.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('assets::filament/exports/asset-movement.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
