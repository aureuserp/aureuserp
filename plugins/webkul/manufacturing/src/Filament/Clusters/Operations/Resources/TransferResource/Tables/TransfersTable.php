<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\TransferResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\TransferResource;

class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return OperationResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn ($record): string => TransferResource::getUrl('view', ['record' => $record], shouldGuessMissingParameters: true)),
                    EditAction::make()
                        ->url(fn ($record): string => TransferResource::getUrl('edit', ['record' => $record], shouldGuessMissingParameters: true)),
                ]),
            ]);
    }
}
