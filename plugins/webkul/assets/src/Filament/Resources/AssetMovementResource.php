<?php

namespace Webkul\Asset\Filament\Resources;

use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Filament\Exports\AssetMovementExporter;
use Webkul\Asset\Filament\Resources\AssetMovementResource\Pages\ListAssetMovements;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetSite;
use Webkul\Support\Enums\NavigationGroup;

class AssetMovementResource extends Resource
{
    protected static ?string $model = AssetMovement::class;

    protected static ?string $slug = 'assets/movements';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::Assets;
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/resources/asset-movement.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('assets::filament/resources/asset-movement.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('assets::filament/resources/asset-movement.plural-label');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'asset.product',
            'fromWarehouse',
            'fromEmployee',
            'fromSite',
            'toWarehouse',
            'toEmployee',
            'toSite',
            'performedBy',
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->defaultSort('occurred_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('occurred_at')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.occurred-at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('asset.asset_tag')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.asset-tag'))
                    ->url(fn (AssetMovement $record): ?string => $record->asset_id ? AssetResource::getUrl('view', ['record' => $record->asset_id]) : null)
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('asset.product.name')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.asset-name'))
                    ->url(fn (AssetMovement $record): ?string => $record->asset_id ? AssetResource::getUrl('view', ['record' => $record->asset_id]) : null)
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('type')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.type'))
                    ->badge(),

                TextColumn::make('quantity')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.quantity'))
                    ->numeric(decimalPlaces: 0)
                    ->sortable()
                    ->summarize(Sum::make()),

                TextColumn::make('source_type')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.source-type'))
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('from_display')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.from'))
                    ->placeholder('—')
                    ->wrap(),

                TextColumn::make('to_display')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.to'))
                    ->placeholder('—')
                    ->wrap(),

                TextColumn::make('condition')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.condition'))
                    ->badge(),

                TextColumn::make('performedBy.name')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.performed-by'))
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('expected_return_at')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.expected-return-at'))
                    ->date()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('note')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.note'))
                    ->limit(50)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->groups([
                Group::make('occurred_at')
                    ->label(__('assets::filament/resources/asset-movement.table.groups.date'))
                    ->date(),
                Group::make('type')
                    ->label(__('assets::filament/resources/asset-movement.table.groups.type')),
                Group::make('asset.product.name')
                    ->label(__('assets::filament/resources/asset-movement.table.groups.asset')),
                Group::make('condition')
                    ->label(__('assets::filament/resources/asset-movement.table.groups.condition')),
                Group::make('performedBy.name')
                    ->label(__('assets::filament/resources/asset-movement.table.groups.performed-by')),
            ])
            ->filters([
                SelectFilter::make('product_type')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.product-type'))
                    ->options(AssetProductType::class)
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('asset.product', fn (Builder $q) => $q->where('type', $data['value']))
                        : $query),

                SelectFilter::make('type')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.type'))
                    ->options(MovementType::class)
                    ->multiple(),

                SelectFilter::make('source_type')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.source-type'))
                    ->options(AssetSourceType::class)
                    ->multiple(),

                SelectFilter::make('condition')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.condition'))
                    ->options(AssetCondition::class)
                    ->multiple(),

                SelectFilter::make('category_id')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.category'))
                    ->relationship('asset.product.category', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => filled($data['values'] ?? $data['value'] ?? null)
                        ? $query->whereHas('asset.product', fn (Builder $q) => AssetCategory::applySubtreeFilter($q, $data['values'] ?? $data['value'], 'category_id'))
                        : $query),

                SelectFilter::make('product_id')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.product'))
                    ->relationship('asset.product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('asset_id')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.asset'))
                    ->relationship('asset', 'asset_tag')
                    ->searchable()
                    ->multiple(),

                SelectFilter::make('warehouse_id')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.warehouse'))
                    ->relationship('toWarehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        $values = Arr::wrap($data['values'] ?? $data['value'] ?? null);

                        if (blank(Arr::first($values, fn ($value): bool => filled($value)))) {
                            return $query;
                        }

                        return $query->where(fn (Builder $q) => $q
                            ->whereIn('to_warehouse_id', $values)
                            ->orWhereIn('from_warehouse_id', $values));
                    }),

                SelectFilter::make('employee_id')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.employee'))
                    ->relationship('toEmployee', 'name')
                    ->searchable()
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        $values = Arr::wrap($data['values'] ?? $data['value'] ?? null);

                        if (blank(Arr::first($values, fn ($value): bool => filled($value)))) {
                            return $query;
                        }

                        return $query->where(fn (Builder $q) => $q
                            ->whereIn('to_employee_id', $values)
                            ->orWhereIn('from_employee_id', $values));
                    }),

                SelectFilter::make('department_id')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.department'))
                    ->relationship('toEmployee.department', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        $values = Arr::wrap($data['values'] ?? $data['value'] ?? null);

                        if (blank(Arr::first($values, fn ($value): bool => filled($value)))) {
                            return $query;
                        }

                        return $query->where(fn (Builder $q) => $q
                            ->whereHas('toEmployee', fn (Builder $eq) => $eq->whereIn('department_id', $values))
                            ->orWhereHas('fromEmployee', fn (Builder $eq) => $eq->whereIn('department_id', $values)));
                    }),

                SelectFilter::make('site_id')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.site'))
                    ->relationship('toSite', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        $values = Arr::wrap($data['values'] ?? $data['value'] ?? null);

                        if (blank(Arr::first($values, fn ($value): bool => filled($value)))) {
                            return $query;
                        }

                        $subtreeIds = AssetSite::subtreeIds($values);

                        return $query->where(fn (Builder $q) => $q->whereIn('to_site_id', $subtreeIds)->orWhereIn('from_site_id', $subtreeIds));
                    }),

                SelectFilter::make('performed_by_user_id')
                    ->label(__('assets::filament/resources/asset-movement.table.filters.performed-by'))
                    ->relationship('performedBy', 'name')
                    ->searchable(),

                Filter::make('occurred_at')
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('assets::filament/resources/asset-movement.table.filters.date-from'))
                            ->native(false),
                        DatePicker::make('until')
                            ->label(__('assets::filament/resources/asset-movement.table.filters.date-until'))
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('occurred_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('occurred_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(__('assets::filament/resources/asset-movement.table.actions.view'))
                    ->schema(static::movementModalSchema(includeAsset: true)),
            ])
            ->toolbarActions([
                AssetMovementExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset-movement.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetMovementExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->visible(fn (): bool => static::canViewAny())
                    ->authorize(fn (): bool => static::canViewAny())
                    ->modifyQueryUsing(fn (Builder $query, array $options): Builder => AssetMovementExporter::applyExportOptions($query, $options))),
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    public static function movementModalSchema(bool $includeAsset = true): array
    {
        $assetComponents = $includeAsset ? [
            TextEntry::make('asset.asset_tag')
                ->label(__('assets::filament/resources/asset-movement.table.columns.asset-tag'))
                ->weight('bold')
                ->url(fn (AssetMovement $record): ?string => $record->asset_id ? AssetResource::getUrl('view', ['record' => $record->asset_id]) : null),

            TextEntry::make('asset.product.name')
                ->label(__('assets::filament/resources/asset-movement.table.columns.asset-name'))
                ->url(fn (AssetMovement $record): ?string => $record->asset_id ? AssetResource::getUrl('view', ['record' => $record->asset_id]) : null),
        ] : [];

        return [
            Section::make(__('assets::filament/resources/asset-movement.infolist.sections.details'))
                ->schema([
                    ...$assetComponents,

                    TextEntry::make('type')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.type'))
                        ->badge(),

                    TextEntry::make('quantity')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.quantity'))
                        ->numeric(decimalPlaces: 0),

                    TextEntry::make('source_type')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.source-type'))
                        ->badge()
                        ->placeholder('—'),

                    TextEntry::make('occurred_at')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.occurred-at'))
                        ->dateTime(),

                    TextEntry::make('condition')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.condition'))
                        ->badge(),

                    TextEntry::make('from_display')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.from'))
                        ->placeholder('—'),

                    TextEntry::make('to_display')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.to'))
                        ->placeholder('—'),

                    TextEntry::make('performedBy.name')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.performed-by'))
                        ->placeholder('—'),

                    TextEntry::make('expected_return_at')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.expected-return-at'))
                        ->date()
                        ->placeholder('—'),

                    TextEntry::make('note')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.note'))
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('assets::filament/resources/asset-movement.infolist.sections.attachments'))
                ->visible(fn (AssetMovement $record): bool => ! empty($record->attachments))
                ->schema([
                    FileUpload::make('attachments')
                        ->label(__('assets::filament/resources/asset-movement.table.columns.attachments'))
                        ->disk('public')
                        ->multiple()
                        ->disabled()
                        ->dehydrated(false)
                        ->downloadable()
                        ->openable()
                        ->previewable()
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetMovements::route('/'),
        ];
    }
}
