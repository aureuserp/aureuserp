<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource\Pages\ListPublicHolidays;
use Webkul\TimeOff\Models\CalendarLeave;

class PublicHolidayResource extends Resource
{
    protected static ?string $model = CalendarLeave::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Group::make()
                        ->schema([
                            Hidden::make('time_type')
                                ->default('leave'),
                            TextInput::make('name')
                                ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.name'))
                                ->required()
                                ->placeholder(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.name-placeholder')),
                        ])->columns(2),

                    Group::make()
                        ->schema([
                            DatePicker::make('date_from')
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
                            DatePicker::make('date_to')
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
                    Select::make('calendar')
                        ->searchable()
                        ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.calendar'))
                        ->preload()
                        ->relationship('calendar', 'name'),
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
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.name')),
                TextColumn::make('date_from')
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.date-from')),
                TextColumn::make('date_to')
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.date-to')),
                TextColumn::make('calendar.name')
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
                SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.company-name')),
                SelectFilter::make('creator_id')
                    ->relationship('createdBy', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.created-by')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.name'))
                            ->icon('heroicon-o-clock'),
                        TextConstraint::make('date_from')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.date-from'))
                            ->icon('heroicon-o-calendar'),
                        TextConstraint::make('date_to')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.date-to'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('created_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.edit.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                ColorEntry::make('color')
                    ->placeholder('—')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.color')),
                TextEntry::make('name')
                    ->placeholder('-')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.name')),
                TextEntry::make('date_from')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.date-from')),
                TextEntry::make('date_to')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.date-to')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPublicHolidays::route('/'),
        ];
    }
}
