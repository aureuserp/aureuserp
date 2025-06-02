<?php

namespace Webkul\Account\Traits;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums;

trait TaxPartition
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('factor_percent')
                    ->suffix('%')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->label(__('accounts::traits/tax-partition.form.factor-percent'))
                    ->live()
                    ->afterStateUpdated(fn (Set $set, $state) => $set('factor', (float) $state / 100)),
                Forms\Components\TextInput::make('factor')
                    ->readOnly()
                    ->label(__('accounts::traits/tax-partition.form.factor-ratio')),
                Forms\Components\Select::make('repartition_type')
                    ->options(Enums\RepartitionType::options())
                    ->required()
                    ->label(__('accounts::traits/tax-partition.form.repartition-type')),
                Forms\Components\Select::make('document_type')
                    ->options(Enums\DocumentType::options())
                    ->required()
                    ->label(__('accounts::traits/tax-partition.form.document-type')),
                Forms\Components\Select::make('account_id')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::traits/tax-partition.form.account')),
                Forms\Components\Select::make('tax_id')
                    ->relationship('tax', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::traits/tax-partition.form.tax')),
                Forms\Components\Toggle::make('use_in_tax_closing')
                    ->label(__('accounts::traits/tax-partition.form.tax-closing-entry')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('factor_percent')
                    ->label(__('accounts::traits/tax-partition.table.columns.factor-percent')),
                Tables\Columns\TextColumn::make('account.name')
                    ->label(__('accounts::traits/tax-partition.table.columns.account')),
                Tables\Columns\TextColumn::make('tax.name')
                    ->label(__('accounts::traits/tax-partition.table.columns.tax')),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('accounts::traits/tax-partition.table.columns.company')),
                Tables\Columns\TextColumn::make('repartition_type')
                    ->formatStateUsing(fn ($state) => Enums\RepartitionType::options()[$state])
                    ->label(__('accounts::traits/tax-partition.table.columns.repartition-type')),
                Tables\Columns\TextColumn::make('document_type')
                    ->formatStateUsing(fn ($state) => Enums\DocumentType::options()[$state])
                    ->label(__('accounts::traits/tax-partition.table.columns.document-type')),
                Tables\Columns\IconColumn::make('use_in_tax_closing')
                    ->boolean()
                    ->label(__('accounts::traits/tax-partition.table.columns.tax-closing-entry')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::traits/tax-partition.table.actions.edit.notification.title'))
                            ->body(__('accounts::traits/tax-partition.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::traits/tax-partition.table.actions.delete.notification.title'))
                            ->body(__('accounts::traits/tax-partition.table.actions.delete.notification.body'))
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function ($data) {
                        $user = Auth::user();

                        $data['creator_id'] = $user->id;

                        $data['company_id'] = $user->default_company_id;

                        $data['factor'] = (float) $data['factor_percent'] / 100;

                        return $data;
                    }),
            ])
            ->reorderable('sort')
            ->modifyQueryUsing(fn ($query) => $query->where('document_type', $this->getDocumentType()));
    }
}
