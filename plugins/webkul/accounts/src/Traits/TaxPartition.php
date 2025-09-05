<?php

namespace Webkul\Account\Traits;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\RepartitionType;

trait TaxPartition
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('factor_percent')
                    ->suffix('%')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->label(__('accounts::traits/tax-partition.form.factor-percent'))
                    ->live()
                    ->afterStateUpdated(fn (Set $set, $state) => $set('factor', (float) $state / 100)),
                TextInput::make('factor')
                    ->readOnly()
                    ->label(__('accounts::traits/tax-partition.form.factor-ratio')),
                Select::make('repartition_type')
                    ->options(RepartitionType::options())
                    ->required()
                    ->label(__('accounts::traits/tax-partition.form.repartition-type')),
                Select::make('document_type')
                    ->options(DocumentType::options())
                    ->required()
                    ->label(__('accounts::traits/tax-partition.form.document-type')),
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::traits/tax-partition.form.account')),
                Select::make('tax_id')
                    ->relationship('tax', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::traits/tax-partition.form.tax')),
                Toggle::make('use_in_tax_closing')
                    ->label(__('accounts::traits/tax-partition.form.tax-closing-entry')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('factor_percent')
                    ->label(__('accounts::traits/tax-partition.table.columns.factor-percent')),
                TextColumn::make('account.name')
                    ->label(__('accounts::traits/tax-partition.table.columns.account')),
                TextColumn::make('tax.name')
                    ->label(__('accounts::traits/tax-partition.table.columns.tax')),
                TextColumn::make('company.name')
                    ->label(__('accounts::traits/tax-partition.table.columns.company')),
                TextColumn::make('repartition_type')
                    ->formatStateUsing(fn ($state) => RepartitionType::options()[$state])
                    ->label(__('accounts::traits/tax-partition.table.columns.repartition-type')),
                TextColumn::make('document_type')
                    ->formatStateUsing(fn ($state) => DocumentType::options()[$state])
                    ->label(__('accounts::traits/tax-partition.table.columns.document-type')),
                IconColumn::make('use_in_tax_closing')
                    ->boolean()
                    ->label(__('accounts::traits/tax-partition.table.columns.tax-closing-entry')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::traits/tax-partition.table.actions.edit.notification.title'))
                            ->body(__('accounts::traits/tax-partition.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::traits/tax-partition.table.actions.delete.notification.title'))
                            ->body(__('accounts::traits/tax-partition.table.actions.delete.notification.body'))
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function ($data) {
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
