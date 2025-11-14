<?php

namespace Webkul\Account\Filament\Resources\JournalResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;

class MovesRelationManager extends RelationManager
{
    protected static string $relationship = 'moveLines';

    protected static ?string $title = 'Journal Entries';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('move_name')
            ->defaultGroup('move_id')
            ->groups([
                Tables\Grouping\Group::make('move_id')
                    ->label('Journal Entry')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn ($record) => $record->move_name)
                    ->collapsible(),
            ])
            ->groupingSettingsHidden()
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('move_name')
                    ->label('Journal Entry')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => $record->move?->getResourceUrl())
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label('Account')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('partner.name')
                    ->label('Partner')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Label')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('debit')
                    ->money()
                    ->summarize(Sum::make()
                        ->money()
                        ->label(''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('credit')
                    ->money()
                    ->summarize(Sum::make()
                        ->money()
                        ->label(''))
                    ->sortable(),
            ]);
    }
}
