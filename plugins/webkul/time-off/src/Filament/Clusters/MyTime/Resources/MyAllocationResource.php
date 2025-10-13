<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\TimeOff\Enums\AllocationType;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource\Pages\CreateMyAllocation;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource\Pages\EditMyAllocation;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource\Pages\ListMyAllocations;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocationResource\Pages\ViewMyAllocation;
use Webkul\TimeOff\Models\LeaveAllocation;

class MyAllocationResource extends Resource
{
    protected static ?string $model = LeaveAllocation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $cluster = MyTime::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'My Allocation';

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-allocation.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-allocation.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
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
                    ])->columns(2)->columnSpanFull(),
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.name'))
                                    ->placeholder(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.name-placeholder'))
                                    ->required(),
                                Grid::make(1)
                                    ->schema([
                                        Select::make('holiday_status_id')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.time-off-type'))
                                            ->relationship('holidayStatus', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ]),
                                Radio::make('allocation_type')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation-type'))
                                    ->options(AllocationType::class)
                                    ->default(AllocationType::REGULAR->value)
                                    ->required(),
                                Fieldset::make(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.validity-period'))
                                    ->schema([
                                        DatePicker::make('date_from')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-from'))
                                            ->native(false)
                                            ->required()
                                            ->default(now()),
                                        DatePicker::make('date_to')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-to'))
                                            ->native(false)
                                            ->placeholder(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-to-placeholder')),
                                    ]),
                                TextInput::make('number_of_days')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->suffix(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation-suffix')),
                                RichEditor::make('notes')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.reason')),
                            ]),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.columns.time-off-type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('number_of_days')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.columns.amount'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('allocation_type')
                    ->formatStateUsing(fn ($state) => AllocationType::options()[$state->value])
                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.table.columns.allocation-type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('state')
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/my-time/resources/my-allocation.table.actions.delete.notification.body'))
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
                    Action::make('refuse')
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.name')),
                                        TextEntry::make('holidayStatus.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.time-off-type')),
                                        TextEntry::make('allocation_type')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-queue-list')
                                            ->formatStateUsing(fn ($state) => AllocationType::options()[$state->value])
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.allocation-type')),
                                    ])->columns(2),
                                Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.title'))
                                    ->schema([
                                        TextEntry::make('date_from')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.date-from'))
                                            ->placeholder('—'),
                                        TextEntry::make('date_to')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.date-to'))
                                            ->placeholder('—'),
                                        TextEntry::make('notes')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.reason'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.title'))
                                ->schema([
                                    TextEntry::make('number_of_days')
                                        ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.entries.allocation'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-calculator')
                                        ->numeric(),
                                    TextEntry::make('state')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-flag')
                                        ->formatStateUsing(fn ($state) => State::options()[$state])
                                        ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.entries.state')),
                                ]),
                        ])->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMyAllocations::route('/'),
            'create' => CreateMyAllocation::route('/create'),
            'edit'   => EditMyAllocation::route('/{record}/edit'),
            'view'   => ViewMyAllocation::route('/{record}'),
        ];
    }
}
