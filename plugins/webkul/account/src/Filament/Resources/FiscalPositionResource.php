<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages;
use Webkul\Account\Filament\Resources\FiscalPositionResource\RelationManagers;
use Webkul\Account\Models\FiscalPosition;

class FiscalPositionResource extends Resource
{
    protected static ?string $model = FiscalPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.name'))
                                    ->required()
                                    ->placeholder(__('Name')),
                                Forms\Components\TextInput::make('foreign_vat')
                                    ->label(__('Foreign VAT'))
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.foreign-vat'))
                                    ->required(),
                                Forms\Components\Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.country')),
                                Forms\Components\Select::make('country_group_id')
                                    ->relationship('countryGroup', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.country-group')),
                                Forms\Components\TextInput::make('zip_from')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.zip-from'))
                                    ->required(),
                                Forms\Components\TextInput::make('zip_to')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.zip-to'))
                                    ->required(),
                                Forms\Components\Toggle::make('auto_reply')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.detect-automatically')),
                            ])->columns(2),
                        Forms\Components\RichEditor::make('notes')
                            ->label(__('accounts::filament/resources/fiscal-position.form.fields.notes')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.name')),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.company')),
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.country')),
                Tables\Columns\TextColumn::make('countryGroup.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.country-group')),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.created-by')),
                Tables\Columns\TextColumn::make('zip_from')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.zip-from')),
                Tables\Columns\TextColumn::make('zip_to')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.zip-to')),
                Tables\Columns\IconColumn::make('auto_reply')
                    ->searchable()
                    ->sortable()
                    ->label(__('Detect Automatically'))
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.detect-automatically')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/fiscal-position.table.columns.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/fiscal-position.table.columns.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/fiscal-position.table.columns.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/fiscal-position.table.columns.bulk-actions.delete.notification.body'))
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
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Grid::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.name'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-document-text'),
                                        Infolists\Components\TextEntry::make('foreign_vat')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.foreign-vat'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-document'),
                                        Infolists\Components\TextEntry::make('country.name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.country'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-globe-alt'),
                                        Infolists\Components\TextEntry::make('countryGroup.name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.country-group'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map'),
                                        Infolists\Components\TextEntry::make('zip_from')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.zip-from'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map-pin'),
                                        Infolists\Components\TextEntry::make('zip_to')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.zip-to'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map-pin'),
                                        Infolists\Components\IconEntry::make('auto_reply')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.detect-automatically'))
                                            ->placeholder('-'),
                                    ])->columns(2),
                            ]),
                        Infolists\Components\TextEntry::make('notes')
                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.notes'))
                            ->placeholder('-')
                            ->markdown(),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewFiscalPosition::class,
            Pages\EditFiscalPosition::class,
            Pages\ManageFiscalPositionTax::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('distribution_for_invoice', [
                RelationManagers\FiscalPositionTaxRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'               => Pages\ListFiscalPositions::route('/'),
            'create'              => Pages\CreateFiscalPosition::route('/create'),
            'view'                => Pages\ViewFiscalPosition::route('/{record}'),
            'edit'                => Pages\EditFiscalPosition::route('/{record}/edit'),
            'fiscal-position-tax' => Pages\ManageFiscalPositionTax::route('/{record}/fiscal-position-tax'),
        ];
    }
}
