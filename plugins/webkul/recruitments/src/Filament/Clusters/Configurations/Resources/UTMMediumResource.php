<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource\Pages\ListUTMMedia;
use Webkul\Recruitment\Models\UTMMedium;

class UTMMediumResource extends Resource
{
    protected static ?string $model = UTMMedium::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-medium.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-medium.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-medium.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('recruitments::filament/clusters/configurations/resources/utm-medium.form.fields.name-placeholder')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.filters.name'))
                            ->icon('heroicon-o-user'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.actions.edit.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/utm-medium.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.infolist.name')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUTMMedia::route('/'),
        ];
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'utm-mediums';
    }
}
