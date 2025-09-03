<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\TimeOff\Enums\AllocationType;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource\Pages;
use Webkul\TimeOff\Models\LeaveAllocation;

class MyAllocationResource extends Resource
{
    protected static ?string $model = LeaveAllocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $cluster = MyTime::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'My Allocation';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-allocation.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-allocation.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        ProgressStepper::make('state')
                            ->hiddenLabel()
                            ->inline()
                            ->options(function ($record) {
                                $onlyStates = [
                                    State::CONFIRM->value,
                                    State::VALIDATE_TWO->value,
                                ];

                                if ($record) {
                                    if ($record->state === State::REFUSE->value) {
                                        $onlyStates[] = State::REFUSE->value;
                                    }
                                }

                                return collect(State::options())->only($onlyStates)->toArray();
                            })
                            ->default(State::CONFIRM->value)
                            ->columnSpan('full')
                            ->disabled()
                            ->reactive()
                            ->live(),
                    ])->columns(2),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.name'))
                                    ->placeholder(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.name-placeholder'))
                                    ->required(),
                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        Forms\Components\Select::make('holiday_status_id')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.time-off-type'))
                                            ->relationship('holidayStatus', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ]),
                                Forms\Components\Radio::make('allocation_type')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation-type'))
                                    ->options(AllocationType::class)
                                    ->default(AllocationType::REGULAR->value)
                                    ->required(),
                                Forms\Components\Fieldset::make(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.validity-period'))
                                    ->schema([
                                        Forms\Components\DatePicker::make('date_from')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-from'))
                                            ->native(false)
                                            ->required()
                                            ->default(now()),
                                        Forms\Components\DatePicker::make('date_to')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-to'))
                                            ->native(false)
                                            ->placeholder(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-to-placeholder')),
                                    ]),
                                Forms\Components\TextInput::make('number_of_days')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->suffix(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation-suffix')),
                                Forms\Components\RichEditor::make('notes')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.reason')),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.columns.time-off-type'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_days')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.columns.amount'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('allocation_type')
                    ->formatStateUsing(fn ($state) => AllocationType::options()[$state])
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.columns.allocation-type'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->formatStateUsing(fn ($state) => State::options()[$state])
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.columns.status'))
                    ->badge()
                    ->sortable()
                    ->searchable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('employee.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.groups.employee-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.groups.time-off-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('allocation_type')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.groups.allocation-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_from')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.groups.start-date'))
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.delete.notification.body'))
                        ),
                    Tables\Actions\Action::make('approve')
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
                                ->title(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.approve.notification.title'))
                                ->body(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.approve.notification.body'))
                                ->send();
                        })
                        ->label(function ($record) {
                            if ($record->state === State::VALIDATE_ONE->value) {
                                return __('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.approve.title.validate');
                            } else {
                                return __('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.approve.title.approve');
                            }
                        }),
                    Tables\Actions\Action::make('refuse')
                        ->icon('heroicon-o-x-circle')
                        ->hidden(fn ($record) => $record->state === State::REFUSE->value)
                        ->color('danger')
                        ->action(function ($record) {
                            $record->update(['state' => State::REFUSE->value]);

                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.refused.notification.title'))
                                ->body(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.refused.notification.body'))
                                ->send();
                        })
                        ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.refused.title')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/my-time/resources/my-allocation.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/my-time/resources/my-allocation.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('employee_id', Auth::user()?->employee?->id);
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.name')),
                                        Infolists\Components\TextEntry::make('holidayStatus.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.time-off-type')),
                                        Infolists\Components\TextEntry::make('allocation_type')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-queue-list')
                                            ->formatStateUsing(fn ($state) => AllocationType::options()[$state])
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.allocation-type')),
                                    ])->columns(2),
                                Infolists\Components\Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('date_from')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.date-from'))
                                            ->placeholder('—'),
                                        Infolists\Components\TextEntry::make('date_to')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.date-to'))
                                            ->placeholder('—'),
                                        Infolists\Components\TextEntry::make('notes')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.reason'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.title'))
                                ->schema([
                                    Infolists\Components\TextEntry::make('number_of_days')
                                        ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.entries.allocation'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-calculator')
                                        ->numeric(),
                                    Infolists\Components\TextEntry::make('state')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-flag')
                                        ->formatStateUsing(fn ($state) => State::options()[$state])
                                        ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.entries.state')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMyAllocations::route('/'),
            'create' => Pages\CreateMyAllocation::route('/create'),
            'edit'   => Pages\EditMyAllocation::route('/{record}/edit'),
            'view'   => Pages\ViewMyAllocation::route('/{record}'),
        ];
    }
}
