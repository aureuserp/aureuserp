<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDayResource\Pages;
use Webkul\TimeOff\Models\LeaveMandatoryDay;

class MandatoryDayResource extends Resource
{
    protected static ?string $model = LeaveMandatoryDay::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('time_off::filament/clusters/configurations/resources/mandatory-days.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time_off::filament/clusters/configurations/resources/mandatory-days.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'start_date',
            'end_date',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('time_off::filament/clusters/configurations/resources/mandatory-days.global-search.name')       => $record->name ?? '—',
            __('time_off::filament/clusters/configurations/resources/mandatory-days.global-search.start-date') => $record->start_date ?? '—',
            __('time_off::filament/clusters/configurations/resources/mandatory-days.global-search.end-date')   => $record->end_date ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\ColorPicker::make('color')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.form.fields.color'))
                    ->required()
                    ->hexColor()
                    ->default('#000000'),
                Forms\Components\TextInput::make('name')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.form.fields.name'))
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->native(false)
                    ->default(now()->format('Y-m-d'))
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.form.fields.start-date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->native(false)
                    ->default(now()->format('Y-m-d'))
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.form.fields.end-date'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.columns.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.columns.created-by'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.columns.start-date'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.columns.end-date'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.filters.company-name')),
                Tables\Filters\SelectFilter::make('creator_id')
                    ->relationship('createdBy', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.filters.created-by')),
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.filters.name'))
                            ->icon('heroicon-o-clock'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('start_date')
                            ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.filters.start-date'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('end_date')
                            ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.filters.end-date'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.groups.company-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('start_date')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.groups.start-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('end_date')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.groups.end-date'))
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.actions.edit.notification.title'))
                            ->body(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.actions.delete.notification.title'))
                            ->body(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.actions.delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.bulk-actions.delete.notification.title'))
                                ->body(__('time_off::filament/clusters/configurations/resources/mandatory-days.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\ColorEntry::make('color')
                    ->placeholder('—')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.color')),
                Infolists\Components\TextEntry::make('name')
                    ->placeholder('-')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.name')),
                Infolists\Components\TextEntry::make('start_date')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.start-date')),
                Infolists\Components\TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time_off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.end-date')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMandatoryDays::route('/'),
        ];
    }
}
