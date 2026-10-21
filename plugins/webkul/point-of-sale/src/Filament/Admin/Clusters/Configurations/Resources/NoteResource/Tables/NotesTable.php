<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\NoteResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/note.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('color')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/note.table.columns.color')),
            ])
            ->recordTitleAttribute('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
