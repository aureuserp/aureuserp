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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Account\Filament\Resources\TaxGroupResource\Pages;
use Webkul\Account\Models\TaxGroup;

class TaxGroupResource extends Resource
{
    protected static ?string $model = TaxGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.company'))
                            ->preload(),
                        Forms\Components\Select::make('country_id')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.country'))
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('preceding_subtotal')
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.preceding-subtotal'))
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.company'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.country'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.created-by'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('preceding_subtotal')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.preceding-subtotal'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/tax-group.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.updated-at'))
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (TaxGroup $record) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('accounts::filament/resources/tax-group.table.actions.delete.notification.error.title'))
                                ->body(__('accounts::filament/resources/tax-group.table.actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/tax-group.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/tax-group.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.success.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('company.name')
                            ->icon('heroicon-o-building-office-2')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.company')),
                        Infolists\Components\TextEntry::make('country.name')
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.country')),
                        Infolists\Components\TextEntry::make('name')
                            ->icon('heroicon-o-tag')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.name')),
                        Infolists\Components\TextEntry::make('preceding_subtotal')
                            ->icon('heroicon-o-rectangle-group')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.preceding-subtotal')),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTaxGroups::route('/'),
            'create' => Pages\CreateTaxGroup::route('/create'),
            'view'   => Pages\ViewTaxGroup::route('/{record}'),
            'edit'   => Pages\EditTaxGroup::route('/{record}/edit'),
        ];
    }
}
