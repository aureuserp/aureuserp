<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\SubLocation;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\StorageCategory;

class PutawayRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(current_company_id())
                    ->live()
                    ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                        'in_location_id'      => Location::class,
                        'out_location_id'     => Location::class,
                        'product_id'          => Product::class,
                        'storage_category_id' => StorageCategory::class,
                    ], $state)),
                Select::make('in_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule.form.fields.in-location'))
                    ->relationship(
                        'inLocation',
                        'full_name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->withTrashed()
                            ->where(owned_by_company($get('company_id')))
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
                    ->relationship(
                        'product',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->where(owned_by_company($get('company_id')))
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
                    ->relationship(
                        'storageCategory',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                    )
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
}
