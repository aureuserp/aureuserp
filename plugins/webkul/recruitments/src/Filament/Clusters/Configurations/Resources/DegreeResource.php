<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DegreeResource\Pages;
use Webkul\Recruitment\Models\Degree;

class DegreeResource extends Resource
{
    protected static ?string $model = Degree::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/degree.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/degree.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/degree.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('recruitments::filament/clusters/configurations/resources/degree.global-search.name') => $record->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('recruitments::filament/clusters/configurations/resources/degree.form.fields.name-placeholder')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/degree.table.filters.name'))
                            ->icon('heroicon-o-user'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/degree.table.actions.edit.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/degree.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/degree.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/degree.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/degree.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/degree.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->reorderable('sort', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.infolist.name')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDegrees::route('/'),
        ];
    }
}
