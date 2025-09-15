<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDayResource\Pages\ListMandatoryDays;
use Webkul\TimeOff\Models\LeaveMandatoryDay;

class MandatoryDayResource extends Resource
{
    protected static ?string $model = LeaveMandatoryDay::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/mandatory-days.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/mandatory-days.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ColorPicker::make('color')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.form.fields.color'))
                    ->required()
                    ->hexColor()
                    ->default('#000000'),
                TextInput::make('name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.form.fields.name'))
                    ->required(),
                DatePicker::make('start_date')
                    ->native(false)
                    ->default(now()->format('Y-m-d'))
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.form.fields.start-date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->native(false)
                    ->default(now()->format('Y-m-d'))
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.form.fields.end-date'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.name'))
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.created-by'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.start-date'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.end-date'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.company-name')),
                SelectFilter::make('creator_id')
                    ->relationship('createdBy', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.created-by')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.name'))
                            ->icon('heroicon-o-clock'),
                        TextConstraint::make('start_date')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.start-date'))
                            ->icon('heroicon-o-calendar'),
                        TextConstraint::make('end_date')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.end-date'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('created_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.name'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.created-by'))
                    ->collapsible(),
                Group::make('company.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.company-name'))
                    ->collapsible(),
                Group::make('start_date')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.start-date'))
                    ->collapsible(),
                Group::make('end_date')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.end-date'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.actions.edit.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.bulk-actions.delete.notification.body')),
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
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.color')),
                TextEntry::make('name')
                    ->placeholder('-')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.name')),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.start-date')),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.end-date')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMandatoryDays::route('/'),
        ];
    }
}
