<?php

namespace Webkul\Partner\Filament\Resources;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Enums\AddressType;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ManageAddresses;
use Webkul\Partner\Models\Partner;

class AddressResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Radio::make('sub_type')
                ->hiddenLabel()
                ->options(AddressType::class)
                ->default(AddressType::INVOICE->value)
                ->inline()
                ->columnSpan(2),
            Select::make('parent_id')
                ->label(__('partners::filament/resources/address.form.partner'))
                ->relationship('parent', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->columnSpan(2)
                ->hiddenOn([ManageAddresses::class])
                ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema)),
            TextInput::make('name')
                ->label(__('partners::filament/resources/address.form.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('partners::filament/resources/address.form.email'))
                ->email()
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('partners::filament/resources/address.form.phone'))
                ->tel()
                ->maxLength(255),
            TextInput::make('mobile')
                ->label(__('partners::filament/resources/address.form.mobile'))
                ->tel(),
            TextInput::make('street1')
                ->label(__('partners::filament/resources/address.form.street1'))
                ->maxLength(255),
            TextInput::make('street2')
                ->label(__('partners::filament/resources/address.form.street2'))
                ->maxLength(255),
            TextInput::make('city')
                ->label(__('partners::filament/resources/address.form.city'))
                ->maxLength(255),
            TextInput::make('zip')
                ->label(__('partners::filament/resources/address.form.zip'))
                ->maxLength(255),
            Select::make('country_id')
                ->label(__('partners::filament/resources/address.form.country'))
                ->relationship(name: 'country', titleAttribute: 'name')
                ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function (Set $set, Get $get) {
                    $set('state_id', null);
                }),
            Select::make('state_id')
                ->label(__('partners::filament/resources/address.form.state'))
                ->relationship(
                    name: 'state',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                )
                ->createOptionForm(function (Schema $schema, Get $get, Set $set) {
                    return $schema
                        ->components([
                            TextInput::make('name')
                                ->label(__('partners::filament/resources/address.form.name'))
                                ->required(),
                            TextInput::make('code')
                                ->label(__('partners::filament/resources/address.form.code'))
                                ->required()
                                ->unique('states'),
                            Select::make('country_id')
                                ->label(__('partners::filament/resources/address.form.country'))
                                ->relationship('country', 'name')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->default($get('country_id'))
                                ->afterStateUpdated(function (Get $get) use ($set) {
                                    $set('country_id', $get('country_id'));
                                }),
                        ]);
                })
                ->searchable()
                ->preload(),
        ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sub_type')
                    ->label(__('partners::filament/resources/address.table.columns.type'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('partners::filament/resources/address.table.columns.name'))
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label(__('partners::filament/resources/address.table.columns.country'))
                    ->searchable(),
                TextColumn::make('state.name')
                    ->label(__('partners::filament/resources/address.table.columns.state'))
                    ->searchable(),
                TextColumn::make('street1')
                    ->label(__('partners::filament/resources/address.table.columns.street1'))
                    ->searchable(),
                TextColumn::make('street2')
                    ->label(__('partners::filament/resources/address.table.columns.street2'))
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('partners::filament/resources/address.table.columns.city'))
                    ->searchable(),
                TextColumn::make('zip')
                    ->label(__('partners::filament/resources/address.table.columns.zip'))
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('partners::filament/resources/address.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['account_type'] = AccountType::ADDRESS;

                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/address.table.header-actions.create.notification.title'))
                            ->body(__('partners::filament/resources/address.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/address.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/address.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/address.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/address.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/address.table.bulk-actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/address.table.bulk-actions.delete.notification.body')),
                    ),
            ]);
    }
}
