<?php

namespace Webkul\Account\Traits;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

trait FiscalPositionTax
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tax_source_id')
                    ->relationship('taxSource', 'name')
                    ->label(__('accounts::traits/fiscal-position-tax.form.fields.tax-source'))
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('tax_destination_id')
                    ->relationship('taxDestination', 'name')
                    ->label(__('accounts::traits/fiscal-position-tax.form.fields.tax-destination'))
                    ->preload()
                    ->searchable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('taxSource.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::traits/fiscal-position-tax.table.columns.tax-source')),
                TextColumn::make('taxDestination.name')
                    ->searchable()
                    ->sortable()
                    ->label('Tax Destination')
                    ->label(__('accounts::traits/fiscal-position-tax.table.columns.tax-destination')),
            ])
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

                        $data['company_id'] = $user?->default_company_id;

                        return $data;
                    }),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('taxSource.name')
                    ->icon('heroicon-o-receipt-percent')
                    ->placeholder('-')
                    ->label(__('accounts::traits/fiscal-position-tax.infolist.entries.tax-source')),
                TextEntry::make('taxDestination.name')
                    ->placeholder('-')
                    ->icon('heroicon-o-receipt-percent')
                    ->label(__('accounts::traits/fiscal-position-tax.infolist.entries.tax-destination')),
            ]);
    }
}
