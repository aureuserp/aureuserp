<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Enums\Applicability;
use Webkul\Account\Filament\Resources\AccountTagResource\Pages\ListAccountTags;
use Webkul\Account\Models\Tag;

class AccountTagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        ColorPicker::make('color')
                            ->label(__('accounts::filament/resources/account-tag.form.fields.color'))
                            ->hexColor(),
                        Select::make('country_id')
                            ->searchable()
                            ->preload()
                            ->label(__('accounts::filament/resources/account-tag.form.fields.country'))
                            ->relationship('country', 'name'),
                        Select::make('applicability')
                            ->options(Applicability::options())
                            ->default(Applicability::ACCOUNT->value)
                            ->label(__('accounts::filament/resources/account-tag.form.fields.applicability'))
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/account-tag.form.fields.name'))
                            ->maxLength(255),
                        Group::make()
                            ->schema([
                                Toggle::make('tax_negate')
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
                ColorColumn::make('color')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.color'))
                    ->searchable(),
                TextColumn::make('country.name')
                    ->numeric()
                    ->maxValue(99999999999)
                    ->label(__('accounts::filament/resources/account-tag.table.columns.country'))
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('applicability')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.applicability'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.name'))
                    ->searchable(),
                IconColumn::make('tax_negate')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.tax-negate'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/account-tag.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title('accounts::filament/clusters/configurations/resources/account-tag.table.actions.edit.notification.title')
                            ->body('accounts::filament/clusters/configurations/resources/account-tag.table.actions.edit.notification.body')
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title('accounts::filament/clusters/configurations/resources/account-tag.table.actions.delete.notification.title')
                            ->body('accounts::filament/clusters/configurations/resources/account-tag.table.actions.delete.notification.body')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title('accounts::filament/clusters/configurations/resources/account-tag.table.bulk-actions.delete.notification.title')
                                ->body('accounts::filament/clusters/configurations/resources/account-tag.table.bulk-actions.delete.notification.body')
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 2])
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.name'))
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('—'),
                        TextEntry::make('color')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.color'))
                            ->formatStateUsing(fn ($state) => "<span style='display:inline-block;width:15px;height:15px;background-color:{$state};border-radius:50%;'></span> ".$state)
                            ->html()
                            ->placeholder('—'),
                        TextEntry::make('applicability')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.applicability'))
                            ->placeholder('—'),
                        TextEntry::make('country.name')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.country'))
                            ->placeholder('—'),
                        IconEntry::make('tax_negate')
                            ->label(__('accounts::filament/resources/account-tag.infolist.entries.tax-negate'))
                            ->boolean(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountTags::route('/'),
        ];
    }
}
