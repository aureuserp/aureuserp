<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Account\Enums\Applicability;
use Webkul\Account\Filament\Resources\AccountTagResource\Pages;
use Webkul\Account\Models\Tag;

class AccountTagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\ColorPicker::make('color')
                            ->label(__('accounts::filament/resources/account-tag.form.fields.color'))
                            ->hexColor(),
                        Forms\Components\Select::make('country_id')
                            ->searchable()
                            ->preload()
                            ->label(__('accounts::filament/resources/account-tag.form.fields.country'))
                            ->relationship('country', 'name'),
                        Forms\Components\Select::make('applicability')
                            ->options(Applicability::options())
                            ->default(Applicability::ACCOUNT->value)
                            ->label(__('accounts::filament/resources/account-tag.form.fields.applicability'))
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/account-tag.form.fields.name'))
                            ->maxLength(255),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Toggle::make('tax_negate')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account-tag.form.fields.tax-negate'))
                                    ->required(),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.color'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->maxValue(99999999999)
                    ->label(__('accounts::filament/resources/account-tag.table.columns.country'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.created-by'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('applicability')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.applicability'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.name'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('tax_negate')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.tax-negate'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/account-tag.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/account-tag.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('country.name')
                    ->label(__('accounts::filament/resources/account-tag.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('accounts::filament/resources/account-tag.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('applicability')
                    ->label(__('accounts::filament/resources/account-tag.table.groups.applicability'))
                    ->collapsible(),
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/account-tag.table.groups.name'))
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title('accounts::filament/clusters/configurations/resources/account-tag.table.actions.edit.notification.title')
                            ->body('accounts::filament/clusters/configurations/resources/account-tag.table.actions.edit.notification.body')
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title('accounts::filament/clusters/configurations/resources/account-tag.table.actions.delete.notification.title')
                            ->body('accounts::filament/clusters/configurations/resources/account-tag.table.actions.delete.notification.body')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title('accounts::filament/clusters/configurations/resources/account-tag.table.bulk-actions.delete.notification.title')
                                ->body('accounts::filament/clusters/configurations/resources/account-tag.table.bulk-actions.delete.notification.body')
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 2])
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.name'))
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('color')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.color'))
                            ->formatStateUsing(fn ($state) => "<span style='display:inline-block;width:15px;height:15px;background-color:{$state};border-radius:50%;'></span> ".$state)
                            ->html()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('applicability')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.applicability'))
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('country.name')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.country'))
                            ->placeholder('—'),
                        Infolists\Components\IconEntry::make('tax_negate')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.tax-negate'))
                            ->boolean(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountTags::route('/'),
        ];
    }
}
