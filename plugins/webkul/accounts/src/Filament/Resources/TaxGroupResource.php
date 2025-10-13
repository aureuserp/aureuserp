<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Account\Filament\Resources\TaxGroupResource\Pages\CreateTaxGroup;
use Webkul\Account\Filament\Resources\TaxGroupResource\Pages\EditTaxGroup;
use Webkul\Account\Filament\Resources\TaxGroupResource\Pages\ListTaxGroups;
use Webkul\Account\Filament\Resources\TaxGroupResource\Pages\ViewTaxGroup;
use Webkul\Account\Models\TaxGroup;

class TaxGroupResource extends Resource
{
    protected static ?string $model = TaxGroup::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.company'))
                            ->preload(),
                        Select::make('country_id')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.country'))
                            ->preload(),
                        TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.name'))
                            ->maxLength(255),
                        TextInput::make('preceding_subtotal')
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.preceding-subtotal'))
                            ->maxLength(255),
                    ])->columns(2),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.company'))
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.country'))
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.name'))
                    ->searchable(),
                TextColumn::make('preceding_subtotal')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.preceding-subtotal'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/tax-group.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.name'))
                    ->collapsible(),
                Group::make('company.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.company'))
                    ->collapsible(),
                Group::make('country.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.country'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.created-by'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.updated-at'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
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
                            ->title(__('accounts::filament/resources/tax-group.table.actions.delete.notification.success.title'))
                            ->body(__('accounts::filament/resources/tax-group.table.actions.delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records, DeleteBulkAction $action) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());

                                $action->success();
                            } catch (QueryException $e) {
                                $action->failure();

                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.success.body'))
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
                        TextEntry::make('company.name')
                            ->icon('heroicon-o-building-office-2')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.company')),
                        TextEntry::make('country.name')
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.country')),
                        TextEntry::make('name')
                            ->icon('heroicon-o-tag')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.name')),
                        TextEntry::make('preceding_subtotal')
                            ->icon('heroicon-o-rectangle-group')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.preceding-subtotal')),
                    ])->columns(2),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxGroups::route('/'),
            'create' => CreateTaxGroup::route('/create'),
            'view'   => ViewTaxGroup::route('/{record}'),
            'edit'   => EditTaxGroup::route('/{record}/edit'),
        ];
    }
}
