<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Webkul\PointOfSale\Models\Category;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.table.columns.parent'))
                    ->placeholder('—')
                    ->searchable(),
                ColorColumn::make('color')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.table.columns.color')),
            ])
            ->groups([
                TableGroup::make('parent.name')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.table.groups.parent')),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordTitleAttribute('name')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (Category $record): bool => $record->trashed()),
                RestoreAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
