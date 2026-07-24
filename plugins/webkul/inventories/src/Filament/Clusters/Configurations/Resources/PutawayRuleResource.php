<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\SubLocation;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource\Pages\ManagePutawayRules;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\PutawayRule;
use Webkul\Inventory\Settings\WarehouseSettings;

class PutawayRuleResource extends Resource
{
    protected static ?string $model = PutawayRule::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-pointing-in';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Configurations::class;

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(WarehouseSettings::class)->enable_locations;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/putaway-rule.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/putaway-rule.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(Auth::user()->default_company_id)
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('in_location_id', null);
                        $set('out_location_id', null);
                    }),
                Select::make('in_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.in-location'))
                    ->relationship(
                        'inLocation',
                        'full_name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->withTrashed()
                            ->where(fn ($q) => $q->where('company_id', $get('company_id'))->orWhereNull('company_id'))
                            ->whereHas('children')
                            ->orderBy('full_name')
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('out_location_id', null)),
                Select::make('out_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.out-location'))
                    ->options(function (Get $get): array {
                        $inLocationId = $get('in_location_id');

                        if (! $inLocationId) {
                            return [];
                        }

                        $inLocation = Location::find($inLocationId);

                        if (! $inLocation) {
                            return [];
                        }

                        return Location::query()
                            ->whereRaw('parent_path LIKE ?', [$inLocation->parent_path.'%'])
                            ->where('id', '!=', $inLocationId)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all();
                    })
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->full_name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->searchable()
                    ->required(),
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.product'))
                    ->relationship('product', 'name')
                    ->relationship(
                        'product',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->withTrashed()
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.product-placeholder'))
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get): void {
                        if ($get('product_id')) {
                            $set('category_id', null);
                        }
                    }),
                Select::make('category_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.category-placeholder'))
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get): void {
                        if ($get('category_id')) {
                            $set('product_id', null);
                        }
                    }),
                Select::make('storage_category_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.storage-category'))
                    ->relationship('storageCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('sub_location')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.sub-location'))
                    ->options(SubLocation::class)
                    ->default(SubLocation::NO)
                    ->native(false)
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.in-location'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.product'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.category'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.storage-category'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('outLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.out-location'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sub_location')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.sub-location'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.company'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (PutawayRule $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePutawayRules::route('/'),
        ];
    }
}
