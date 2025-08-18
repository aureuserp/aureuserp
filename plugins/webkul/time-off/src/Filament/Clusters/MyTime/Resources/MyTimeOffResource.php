<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages\CreateMyTimeOff;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages\EditMyTimeOff;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages\ListMyTimeOffs;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages\ViewMyTimeOff;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Models\LeaveType;

class MyTimeOffResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = MyTime::class;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-time-off.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-time-off.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('holiday_status_id')
                                    ->relationship('holidayStatus', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.time-off-type'))
                                    ->required(),
                                Fieldset::make()
                                    ->label(function (Get $get) {
                                        if ($get('request_unit_half')) {
                                            return __('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.date');
                                        } else {
                                            return __('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.dates');
                                        }
                                    })
                                    ->live()
                                    ->schema([
                                        DatePicker::make('request_date_from')
                                            ->native(false)
                                            ->default(now())
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.request-date-from'))
                                            ->required(),
                                        DatePicker::make('request_date_to')
                                            ->native(false)
                                            ->default(now())
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.request-date-to'))
                                            ->hidden(fn (Get $get) => $get('request_unit_half'))
                                            ->required(),
                                        Select::make('request_date_from_period')
                                            ->options(RequestDateFromPeriod::class)
                                            ->default(RequestDateFromPeriod::MORNING->value)
                                            ->native(false)
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.period'))
                                            ->visible(fn (Get $get) => $get('request_unit_half'))
                                            ->required(),
                                    ]),
                                Toggle::make('request_unit_half')
                                    ->live()
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.half-day')),
                                Placeholder::make('requested_days')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.requested-days'))
                                    ->live()
                                    ->inlineLabel()
                                    ->reactive()
                                    ->content(function ($state, Get $get): string {
                                        if ($get('request_unit_half')) {
                                            return __('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.day', ['day' => '0.5']);
                                        }

                                        $startDate = Carbon::parse($get('request_date_from'));
                                        $endDate = $get('request_date_to') ? Carbon::parse($get('request_date_to')) : $startDate;

                                        return __('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.days', ['days' => $startDate->diffInDays($endDate) + 1]);
                                    }),
                                Textarea::make('private_name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.description'))
                                    ->live(),
                                FileUpload::make('attachment')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.attachment'))
                                    ->visible(function (Get $get) {
                                        $leaveType = LeaveType::find($get('holiday_status_id'));

                                        if ($leaveType) {
                                            return $leaveType->support_document;
                                        }

                                        return false;
                                    })
                                    ->live(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.employee-name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.time-off-type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('private_name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.description'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date_from')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.date-from'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date_to')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.date-to'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('duration_display')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.duration'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('state')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.status'))
                    ->formatStateUsing(fn ($state) => State::options()[$state])
                    ->sortable()
                    ->badge()
                    ->searchable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('employee.name')
                    ->label(__('Employee Name'))
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.employee-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.time-off-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_from')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.start-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_to')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.start-to'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.delete.notification.body'))
                    ),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn ($record) => $record->state === State::VALIDATE_TWO->value)
                    ->action(function ($record) {
                        if ($record->state === State::VALIDATE_ONE->value) {
                            $record->update(['state' => State::VALIDATE_TWO->value]);
                        } else {
                            $record->update(['state' => State::VALIDATE_TWO->value]);
                        }

                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.approve.notification.title'))
                            ->body(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.approve.notification.body'))
                            ->send();
                    })
                    ->label(function ($record) {
                        if ($record->state === State::VALIDATE_ONE->value) {
                            return __('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.approve.title.validate');
                        } else {
                            return __('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.approve.title.approve');
                        }
                    }),
                Action::make('refuse')
                    ->icon('heroicon-o-x-circle')
                    ->hidden(fn ($record) => $record->state === State::REFUSE->value)
                    ->color('danger')
                    ->action(function ($record) {
                        $record->update(['state' => State::REFUSE->value]);

                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.refused.notification.title'))
                            ->body(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.refused.notification.body'))
                            ->send();
                    })
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.refused.title')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/my-time/resources/my-time-off.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/my-time/resources/my-time-off.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('employee_id', Auth::user()?->employee?->id);
            });
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMyTimeOffs::route('/'),
            'create' => CreateMyTimeOff::route('/create'),
            'edit'   => EditMyTimeOff::route('/{record}/edit'),
            'view'   => ViewMyTimeOff::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('holidayStatus.name')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.time-off-type'))
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('request_unit_half')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.half-day'))
                                    ->formatStateUsing(fn ($record) => $record->request_unit_half ? 'Yes' : 'No')
                                    ->icon('heroicon-o-clock'),
                                TextEntry::make('request_date_from')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.request-date-from'))
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('request_date_to')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.request-date-to'))
                                    ->date()
                                    ->hidden(fn ($record) => $record->request_unit_half)
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('request_date_from_period')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.period'))
                                    ->visible(fn ($record) => $record->request_unit_half)
                                    ->icon('heroicon-o-sun'),
                                TextEntry::make('private_name')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.description'))
                                    ->icon('heroicon-o-document-text'),
                                TextEntry::make('duration_display')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.requested-days'))
                                    ->formatStateUsing(function ($record) {
                                        if ($record->request_unit_half) {
                                            return __('time-off::filament/clusters/management/resources/time-off.infolist.entries.day', ['day' => '0.5']);
                                        }

                                        $startDate = Carbon::parse($record->request_date_from);
                                        $endDate = $record->request_date_to ? Carbon::parse($record->request_date_to) : $startDate;

                                        return __('time-off::filament/clusters/management/resources/time-off.infolist.entries.days', ['days' => ($startDate->diffInDays($endDate) + 1)]);
                                    })
                                    ->icon('heroicon-o-calendar-days'),
                                ImageEntry::make('attachment')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.attachment'))
                                    ->visible(fn ($record) => $record->holidayStatus?->support_document),
                            ]),
                    ]),
            ]);
    }
}
