<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
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
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\CreateFiscalPosition;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\EditFiscalPosition;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ListFiscalPositions;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ManageFiscalPositionTax;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ViewFiscalPosition;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class FiscalPositionResource extends Resource
{
    protected static ?string $model = FiscalPosition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

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
                                Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.company'))
                                    ->required(),

                                Toggle::make('auto_reply')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.detect-automatically')),
                            ])->columns(2),
                        RichEditor::make('notes')
                            ->label(__('accounts::filament/resources/fiscal-position.form.fields.notes')),
                    ])->columnSpanFull(),
                Tabs::make('Mappings')
                    ->tabs([
                        Tab::make('Tax Mapping')
                            ->schema([
                                Repeater::make('taxes')
                                    ->hiddenLabel()
                                    ->relationship('taxes')
                                    ->compact()
                                    ->reactive()
                                    ->addActionLabel(__('Add Tax Mapping'))
                                    ->table([
                                        TableColumn::make('tax_source_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.tax-mapping.table.columns.tax-source'))
                                            ->width(200),

                                        TableColumn::make('tax_destination_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.tax-mapping.table.columns.tax-destination'))
                                            ->width(200),
                                    ])
                                    ->schema([
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
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('Account Mapping')
                            ->schema([
                                Repeater::make('accounts')
                                    ->hiddenLabel()
                                    ->relationship('accounts')
                                    ->compact()
                                    ->reactive()
                                    ->addActionLabel(__('Add Account Mapping'))
                                    ->table([
                                        TableColumn::make('account_source_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.source-account'))
                                            ->width(200),
                                        TableColumn::make('account_destination_id')
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.destination-account'))
                                            ->width(200),
                                    ])
                                    ->schema([
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
                                    ])
                                    ->columns(2),
                            ]),
                    ])->columnSpanFull(),

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
                TextColumn::make('creator.name')
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
                    ])->columnSpanFull(),
                Tabs::make('Mappings')
                    ->tabs([
                        Tab::make('Tax Mapping')
                            ->schema([
                                RepeatableEntry::make('taxes')
                                    ->hiddenLabel()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('taxSource.name')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.tax-mapping.table.columns.tax-source')),

                                        InfolistTableColumn::make('taxDestination.name')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.tax-mapping.table.columns.tax-destination')),
                                    ])
                                    ->schema([
                                        TextEntry::make('taxSource.name')
                                            ->placeholder('-'),

                                        TextEntry::make('taxDestination.name')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                        Tab::make('Account Mapping')
                            ->schema([
                                RepeatableEntry::make('accounts')
                                    ->hiddenLabel()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('accountSource.name')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.source-account')),

                                        InfolistTableColumn::make('accountDestination.name')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/fiscal-position.form.tabs.account-mapping.table.columns.destination-account')),
                                    ])
                                    ->schema([
                                        TextEntry::make('accountSource.name')
                                            ->placeholder('-'),

                                        TextEntry::make('accountDestination.name')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
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
