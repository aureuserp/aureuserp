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
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource\Pages;
use Webkul\TimeOff\Models\CalendarLeave;

class PublicHolidayResource extends Resource
{
    protected static ?string $model = CalendarLeave::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Public Holiday';

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/public-holiday.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/public-holiday.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Hidden::make('time_type')
                                ->default('leave'),
                            Forms\Components\TextInput::make('name')
                                ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.name'))
                                ->required()
                                ->placeholder(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.name-placeholder')),
                        ])->columns(2),

                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\DatePicker::make('date_from')
                                ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.date-from'))
                                ->native(false)
                                ->required()
                                ->minDate(now()->toDateString())
                                ->live()
                                ->afterStateUpdated(function (callable $set, $state) {
                                    $set('date_to', null);
                                })
                                ->rules([
                                    'required',
                                    'date',
                                    'after_or_equal:today',
                                ]),
                            Forms\Components\DatePicker::make('date_to')
                                ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.date-to'))
                                ->required()
                                ->native(false)
                                ->minDate(function (callable $get) {
                                    return $get('date_from') ?: now()->toDateString();
                                })
                                ->live()
                                ->rules([
                                    'required',
                                    'date',
                                    'after_or_equal:date_from',
                                ]),
                        ])->columns(2),
                    Forms\Components\Select::make('calendar')
                        ->searchable()
                        ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.calendar'))
                        ->preload()
                        ->relationship('calendar', 'name'),
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
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.name')),
                Tables\Columns\TextColumn::make('date_from')
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.date-from')),
                Tables\Columns\TextColumn::make('date_to')
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.date-to')),
                Tables\Columns\TextColumn::make('calendar.name')
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.calendar')),
            ])
            ->groups([
                Tables\Grouping\Group::make('date_from')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.groups.date-from'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_to')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.groups.date-to'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.groups.company-name'))
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.company-name')),
                Tables\Filters\SelectFilter::make('creator_id')
                    ->relationship('createdBy', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.created-by')),
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.name'))
                            ->icon('heroicon-o-clock'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('date_from')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.date-from'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('date_to')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.date-to'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.updated-at')),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.edit.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.bulk-actions.delete.notification.body')),
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
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.color')),
                Infolists\Components\TextEntry::make('name')
                    ->placeholder('-')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.name')),
                Infolists\Components\TextEntry::make('date_from')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.date-from')),
                Infolists\Components\TextEntry::make('date_to')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.date-to')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPublicHolidays::route('/'),
        ];
    }
}
