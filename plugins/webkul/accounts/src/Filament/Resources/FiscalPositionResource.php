<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\CreateFiscalPosition;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\EditFiscalPosition;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ListFiscalPositions;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ManageFiscalPositionTax;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ViewFiscalPosition;
use Webkul\Account\Filament\Resources\FiscalPositionResource\RelationManagers\FiscalPositionTaxRelationManager;
use Webkul\Account\Models\FiscalPosition;

class FiscalPositionResource extends Resource
{
    protected static ?string $model = FiscalPosition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.name'))
                                    ->required()
                                    ->placeholder(__('Name')),
                                TextInput::make('foreign_vat')
                                    ->label(__('Foreign VAT'))
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.foreign-vat'))
                                    ->required(),
                                Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.country')),
                                Select::make('country_group_id')
                                    ->relationship('countryGroup', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.country-group')),
                                TextInput::make('zip_from')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.zip-from'))
                                    ->required(),
                                TextInput::make('zip_to')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.zip-to'))
                                    ->required(),
                                Toggle::make('auto_reply')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.detect-automatically')),
                            ])->columns(2),
                        RichEditor::make('notes')
                            ->label(__('accounts::filament/resources/fiscal-position.form.fields.notes')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.name')),
                TextColumn::make('company.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.company')),
                TextColumn::make('country.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.country')),
                TextColumn::make('countryGroup.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.country-group')),
                TextColumn::make('createdBy.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.created-by')),
                TextColumn::make('zip_from')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.zip-from')),
                TextColumn::make('zip_to')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.zip-to')),
                IconColumn::make('auto_reply')
                    ->searchable()
                    ->sortable()
                    ->label(__('Detect Automatically'))
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.detect-automatically')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/fiscal-position.table.columns.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/fiscal-position.table.columns.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/fiscal-position.table.columns.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/fiscal-position.table.columns.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.name'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-document-text'),
                                        TextEntry::make('foreign_vat')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.foreign-vat'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-document'),
                                        TextEntry::make('country.name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.country'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-globe-alt'),
                                        TextEntry::make('countryGroup.name')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.country-group'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map'),
                                        TextEntry::make('zip_from')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.zip-from'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map-pin'),
                                        TextEntry::make('zip_to')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.zip-to'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-map-pin'),
                                        IconEntry::make('auto_reply')
                                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.detect-automatically'))
                                            ->placeholder('-'),
                                    ])->columns(2),
                            ]),
                        TextEntry::make('notes')
                            ->label(__('accounts::filament/resources/fiscal-position.infolist.entries.notes'))
                            ->placeholder('-')
                            ->markdown(),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewFiscalPosition::class,
            EditFiscalPosition::class,
            ManageFiscalPositionTax::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('distribution_for_invoice', [
                FiscalPositionTaxRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'               => ListFiscalPositions::route('/'),
            'create'              => CreateFiscalPosition::route('/create'),
            'view'                => ViewFiscalPosition::route('/{record}'),
            'edit'                => EditFiscalPosition::route('/{record}/edit'),
            'fiscal-position-tax' => ManageFiscalPositionTax::route('/{record}/fiscal-position-tax'),
        ];
    }
}
