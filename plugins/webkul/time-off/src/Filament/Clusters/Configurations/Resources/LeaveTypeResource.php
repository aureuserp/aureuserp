<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\TimeOff\Enums\AllocationValidationType;
use Webkul\TimeOff\Enums\EmployeeRequest;
use Webkul\TimeOff\Enums\LeaveValidationType;
use Webkul\TimeOff\Enums\RequestUnit;
use Webkul\TimeOff\Enums\RequiresAllocation;
use Webkul\TimeOff\Enums\TimeType;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource\Pages\CreateLeaveType;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource\Pages\EditLeaveType;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource\Pages\ListLeaveTypes;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource\Pages\ViewLeaveType;
use Webkul\TimeOff\Models\LeaveType;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/leave-type.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                        Group::make()
                                            ->schema([
                                                Group::make()
                                                    ->schema([
                                                        Radio::make('leave_validation_type')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.approval'))
                                                            ->inline(false)
                                                            ->default(LeaveValidationType::HR->value)
                                                            ->live()
                                                            ->options(LeaveValidationType::class),
                                                        Radio::make('requires_allocation')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.requires-allocation'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->default(RequiresAllocation::NO->value)
                                                            ->options(RequiresAllocation::class),
                                                        Radio::make('employee_requests')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.employee-requests'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->visible(fn (Get $get) => $get('requires_allocation') === RequiresAllocation::YES)
                                                            ->default(EmployeeRequest::NO->value)
                                                            ->options(EmployeeRequest::class),
                                                        Radio::make('allocation_validation_type')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.approval'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->visible(fn (Get $get) => $get('requires_allocation') === RequiresAllocation::YES)
                                                            ->default(AllocationValidationType::HR->value)
                                                            ->options(AllocationValidationType::class),
                                                    ]),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.display-option.title'))
                                    ->hiddenLabel()
                                    ->schema([
                                        ColorPicker::make('color')
                                            ->required()
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.display-option.fields.color'))
                                            ->hexColor(),
                                    ]),
                                Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.title'))
                                    ->hiddenLabel()
                                    ->schema([
                                        Select::make('time_off_user_leave_types')
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.notified-time-off-officers'))
                                            ->relationship('notifiedTimeOffOfficers', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->multiple(),
                                        Select::make('request_unit')
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.take-time-off-in'))
                                            ->options(RequestUnit::class)
                                            ->default(RequestUnit::DAY->value),
                                        Toggle::make('include_public_holidays_in_duration')
                                            ->inline(false)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.public-holiday-included')),
                                        Toggle::make('support_document')
                                            ->inline(false)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.allow-to-attach-supporting-document')),
                                        Toggle::make('show_on_dashboard')
                                            ->inline(false)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.show-on-dashboard')),
                                        Select::make('time_type')
                                            ->options(TimeType::class)
                                            ->default(TimeType::LEAVE->value)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.kind-of-time')),
                                        Toggle::make('allows_negative')
                                            ->visible(fn (Get $get) => $get('requires_allocation') === RequiresAllocation::YES)
                                            ->live()
                                            ->inline(false)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.allow-negative-cap')),
                                        TextInput::make('max_allowed_negative')
                                            ->numeric()
                                            ->default(0)
                                            ->visible(fn (Get $get) => $get('requires_allocation') === RequiresAllocation::YES && $get('allows_negative') === true)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.max-negative-cap'))
                                            ->step(1)
                                            ->live()
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('name')
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('leave_validation_type')
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.time-off-approval'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('notifiedTimeOffOfficers.name')
                    ->badge()
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.notified-time-officers'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('requires_allocation')
                    ->badge()
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.requires-allocation'))
                    ->formatStateUsing(fn ($state) => RequiresAllocation::options()[$state])
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('allocation_validation_type')
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.allocation-approval'))
                    ->searchable()
                    ->formatStateUsing(fn ($state) => AllocationValidationType::options()[$state])
                    ->sortable(),
                TextColumn::make('employee_requests')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.employee-request'))
                    ->formatStateUsing(fn ($state) => EmployeeRequest::options()[$state])
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('color')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.color')),
                TextColumn::make('company.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.name'))
                            ->icon('heroicon-o-building-office-2'),
                        TextConstraint::make('leave_validation_type')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.time-off-approval'))
                            ->icon('heroicon-o-check-circle'),
                        TextConstraint::make('requires_allocation')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.requires-allocation'))
                            ->icon('heroicon-o-calculator'),
                        TextConstraint::make('employee_requests')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.employee-request'))
                            ->icon('heroicon-o-user-group'),
                        TextConstraint::make('time_type')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.time-type'))
                            ->icon('heroicon-o-clock'),
                        TextConstraint::make('request_unit')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.request-unit'))
                            ->icon('heroicon-o-clock'),
                        RelationshipConstraint::make('created_by')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.company-name'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.actions.delete.notification.body'))
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.actions.restore.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.actions.restore.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.force-delete.notification.success.title'))
                                    ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.name'))
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—')
                                            ->size(TextSize::Large),
                                        Group::make()
                                            ->schema([
                                                Group::make()
                                                    ->schema([
                                                        TextEntry::make('leave_validation_type')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.approval'))
                                                            ->icon('heroicon-o-check-circle')
                                                            ->placeholder('—')
                                                            ->badge(),
                                                        TextEntry::make('requires_allocation')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.requires-allocation'))
                                                            ->icon('heroicon-o-calculator')
                                                            ->placeholder('—')
                                                            ->formatStateUsing(fn ($state) => RequiresAllocation::options()[$state])
                                                            ->badge(),
                                                        TextEntry::make('employee_requests')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.employee-requests'))
                                                            ->icon('heroicon-o-user-group')
                                                            ->placeholder('—')
                                                            ->formatStateUsing(fn ($state) => EmployeeRequest::options()[$state])
                                                            ->visible(fn ($record) => $record->requires_allocation === RequiresAllocation::YES->value)
                                                            ->badge(),
                                                        TextEntry::make('allocation_validation_type')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.approval'))
                                                            ->icon('heroicon-o-shield-check')
                                                            ->placeholder('—')
                                                            ->formatStateUsing(fn ($state) => AllocationValidationType::options()[$state])
                                                            ->visible(fn ($record) => $record->requires_allocation === RequiresAllocation::YES->value)
                                                            ->badge(),
                                                    ]),
                                            ]),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.display-option.title'))
                                ->schema([
                                    ColorEntry::make('color')
                                        ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.display-option.entries.color'))
                                        ->placeholder('—'),
                                ]),
                            Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.title'))
                                ->schema([
                                    TextEntry::make('notifiedTimeOffOfficers')
                                        ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.notified-time-off-officers'))
                                        ->icon('heroicon-o-bell-alert')
                                        ->placeholder('—')
                                        ->listWithLineBreaks()
                                        ->getStateUsing(function ($record) {
                                            return $record->notifiedTimeOffOfficers->pluck('name')->join(', ') ?: '—';
                                        }),
                                    TextEntry::make('request_unit')
                                        ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.take-time-off-in'))
                                        ->icon('heroicon-o-clock')
                                        ->formatStateUsing(fn ($state) => RequestUnit::options()[$state])
                                        ->placeholder('—')
                                        ->badge(),
                                    IconEntry::make('include_public_holidays_in_duration')
                                        ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.public-holiday-included'))
                                        ->boolean()
                                        ->placeholder('—'),
                                    IconEntry::make('support_document')
                                        ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.allow-to-attach-supporting-document'))
                                        ->boolean()
                                        ->placeholder('—'),
                                    IconEntry::make('show_on_dashboard')
                                        ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.show-on-dashboard'))
                                        ->boolean()
                                        ->placeholder('—'),
                                    TextEntry::make('time_type')
                                        ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.kind-of-time'))
                                        ->icon('heroicon-o-clock')
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state) => TimeType::options()[$state])
                                        ->badge(),
                                    IconEntry::make('allows_negative')
                                        ->boolean()
                                        ->visible(fn ($record) => $record->requires_allocation === RequiresAllocation::YES->value)
                                        ->placeholder('—'),
                                    TextEntry::make('max_allowed_negative')
                                        ->label(__('time-off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.max-negative-cap'))
                                        ->icon('heroicon-o-arrow-trending-down')
                                        ->placeholder('—')
                                        ->visible(fn ($record) => $record->requires_allocation === RequiresAllocation::YES->value && $record->allows_negative === true)
                                        ->numeric(),
                                ]),
                        ])->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLeaveTypes::route('/'),
            'create' => CreateLeaveType::route('/create'),
            'view'   => ViewLeaveType::route('/{record}'),
            'edit'   => EditLeaveType::route('/{record}/edit'),
        ];
    }
}
