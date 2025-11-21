<?php

namespace Webkul\Account\Filament\Resources\FiscalPositionResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class FiscalPositionAccountRelationManager extends RelationManager
{
    protected static string $relationship = 'accounts';

    protected static ?string $title = 'Fiscal Position Accounts';

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('account_source_id')
                ->label('Source Account')
                ->searchable()
                ->preload()
                ->relationship('accountSource', 'name')
                ->required(),
            Select::make('account_destination_id')
                ->label('Destination Account')
                ->searchable()
                ->preload()
                ->relationship('accountDestination', 'name')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('accountSource.name')->label('Source Account'),
                TextColumn::make('accountDestination.name')->label('Destination Account'),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::traits/fiscal-position-tax.table.actions.edit.notification.title'))
                            ->title(__('accounts::traits/fiscal-position-tax.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::traits/fiscal-position-tax.table.actions.delete.notification.title'))
                            ->title(__('accounts::traits/fiscal-position-tax.table.actions.delete.notification.body'))
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::traits/fiscal-position-tax.table.header-actions.create.notification.title'))
                            ->title(__('accounts::traits/fiscal-position-tax.table.header-actions.create.notification.body'))
                    )
                    ->mutateDataUsing(function ($data) {
                        $user = Auth::user();

                        $data['creator_id'] = $user->id;

                        return $data;
                    }),
            ]);
    }
}
