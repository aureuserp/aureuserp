<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\TimeOff\Enums;
use Webkul\TimeOff\Enums\RequiresAllocation;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource\Pages;
use Webkul\TimeOff\Models\LeaveType;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('time_off::filament/clusters/configurations/resources/leave-type.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'company.name',
            'createdBy.name',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('time_off::filament/clusters/configurations/resources/leave-type.global-search.name')            => $record->name ?? '—',
            __('time_off::filament/clusters/configurations/resources/leave-type.global-search.company')         => $record->company?->name ?? '—',
            __('time_off::filament/clusters/configurations/resources/leave-type.global-search.created-by')      => $record->createdBy?->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.general.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Group::make()
                                                    ->schema([
                                                        Forms\Components\Radio::make('leave_validation_type')
                                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.approval'))
                                                            ->inline(false)
                                                            ->default(Enums\LeaveValidationType::HR->value)
                                                            ->live()
                                                            ->options(Enums\LeaveValidationType::class),
                                                        Forms\Components\Radio::make('requires_allocation')
                                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.requires-allocation'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->default(Enums\RequiresAllocation::NO->value)
                                                            ->options(Enums\RequiresAllocation::class),
                                                        Forms\Components\Radio::make('employee_requests')
                                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.employee-requests'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->visible(fn (Get $get) => $get('requires_allocation') === Enums\RequiresAllocation::YES->value)
                                                            ->default(Enums\EmployeeRequest::NO->value)
                                                            ->options(Enums\EmployeeRequest::class),
                                                        Forms\Components\Radio::make('allocation_validation_type')
                                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.approval'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->visible(fn (Get $get) => $get('requires_allocation') === Enums\RequiresAllocation::YES->value)
                                                            ->default(Enums\AllocationValidationType::HR->value)
                                                            ->options(Enums\AllocationValidationType::class),
                                                    ]),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.display-option.title'))
                                    ->hiddenLabel()
                                    ->schema([
                                        Forms\Components\ColorPicker::make('color')
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.display-option.fields.color'))
                                            ->hexColor(),
                                    ]),
                                Forms\Components\Section::make(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.title'))
                                    ->hiddenLabel()
                                    ->schema([
                                        Forms\Components\Select::make('time_off_user_leave_types')
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.notified-time-off-officers'))
                                            ->relationship('notifiedTimeOffOfficers', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->multiple(),
                                        Forms\Components\Select::make('request_unit')
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.take-time-off-in'))
                                            ->options(Enums\RequestUnit::class)
                                            ->default(Enums\RequestUnit::DAY->value),
                                        Forms\Components\Toggle::make('include_public_holidays_in_duration')
                                            ->inline(false)
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.public-holiday-included')),
                                        Forms\Components\Toggle::make('support_document')
                                            ->inline(false)
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.allow-to-attach-supporting-document')),
                                        Forms\Components\Toggle::make('show_on_dashboard')
                                            ->inline(false)
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.show-on-dashboard')),
                                        Forms\Components\Select::make('time_type')
                                            ->options(Enums\TimeType::class)
                                            ->default(Enums\TimeType::LEAVE->value)
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.kind-of-time')),
                                        Forms\Components\Toggle::make('allows_negative')
                                            ->visible(fn (Get $get) => $get('requires_allocation') === Enums\RequiresAllocation::YES->value)
                                            ->live()
                                            ->inline(false)
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.allow-negative-cap')),
                                        Forms\Components\TextInput::make('max_allowed_negative')
                                            ->numeric()
                                            ->default(0)
                                            ->visible(fn (Get $get) => $get('requires_allocation') === Enums\RequiresAllocation::YES->value && $get('allows_negative') === true)
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.max-negative-cap'))
                                            ->step(1)
                                            ->live()
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('leave_validation_type')
                    ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.columns.time-off-approval'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('notifiedTimeOffOfficers.name')
                    ->badge()
                    ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.columns.notified-time-officers'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('requires_allocation')
                    ->badge()
                    ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.columns.requires-allocation'))
                    ->formatStateUsing(fn ($state) => RequiresAllocation::options()[$state])
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('allocation_validation_type')
                    ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.columns.allocation-approval'))
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Enums\AllocationValidationType::options()[$state])
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee_requests')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.columns.employee-request'))
                    ->formatStateUsing(fn ($state) => Enums\EmployeeRequest::options()[$state])
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.columns.color')),
                Tables\Columns\TextColumn::make('company.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.name'))
                            ->icon('heroicon-o-building-office-2'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('leave_validation_type')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.time-off-approval'))
                            ->icon('heroicon-o-check-circle'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('requires_allocation')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.requires-allocation'))
                            ->icon('heroicon-o-calculator'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('employee_requests')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.employee-request'))
                            ->icon('heroicon-o-user-group'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('time_type')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.time-type'))
                            ->icon('heroicon-o-clock'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('request_unit')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.request-unit'))
                            ->icon('heroicon-o-clock'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('created_by')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.company-name'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time_off::filament/clusters/configurations/resources/leave-type.table.actions.delete.notification.title'))
                            ->body(__('time_off::filament/clusters/configurations/resources/leave-type.table.actions.delete.notification.body'))
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time_off::filament/clusters/configurations/resources/leave-type.table.actions.restore.notification.title'))
                            ->body(__('time_off::filament/clusters/configurations/resources/leave-type.table.actions.restore.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time_off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('time_off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.delete.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time_off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('time_off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.force-delete.notification.body'))
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time_off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('time_off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.name'))
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—')
                                            ->size(TextEntrySize::Large),
                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\Group::make()
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('leave_validation_type')
                                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.approval'))
                                                            ->icon('heroicon-o-check-circle')
                                                            ->placeholder('—')
                                                            ->badge(),
                                                        Infolists\Components\TextEntry::make('requires_allocation')
                                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.requires-allocation'))
                                                            ->icon('heroicon-o-calculator')
                                                            ->placeholder('—')
                                                            ->formatStateUsing(fn ($state) => Enums\RequiresAllocation::options()[$state])
                                                            ->badge(),
                                                        Infolists\Components\TextEntry::make('employee_requests')
                                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.employee-requests'))
                                                            ->icon('heroicon-o-user-group')
                                                            ->placeholder('—')
                                                            ->formatStateUsing(fn ($state) => Enums\EmployeeRequest::options()[$state])
                                                            ->visible(fn ($record) => $record->requires_allocation === Enums\RequiresAllocation::YES->value)
                                                            ->badge(),
                                                        Infolists\Components\TextEntry::make('allocation_validation_type')
                                                            ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.general.entries.approval'))
                                                            ->icon('heroicon-o-shield-check')
                                                            ->placeholder('—')
                                                            ->formatStateUsing(fn ($state) => Enums\AllocationValidationType::options()[$state])
                                                            ->visible(fn ($record) => $record->requires_allocation === Enums\RequiresAllocation::YES->value)
                                                            ->badge(),
                                                    ]),
                                            ]),
                                    ]),
                            ])->columnSpan(2),
                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.display-option.title'))
                                ->schema([
                                    Infolists\Components\ColorEntry::make('color')
                                        ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.display-option.entries.color'))
                                        ->placeholder('—'),
                                ]),
                            Infolists\Components\Section::make(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.title'))
                                ->schema([
                                    Infolists\Components\TextEntry::make('notifiedTimeOffOfficers')
                                        ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.notified-time-off-officers'))
                                        ->icon('heroicon-o-bell-alert')
                                        ->placeholder('—')
                                        ->listWithLineBreaks()
                                        ->getStateUsing(function ($record) {
                                            return $record->notifiedTimeOffOfficers->pluck('name')->join(', ') ?: '—';
                                        }),
                                    Infolists\Components\TextEntry::make('request_unit')
                                        ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.take-time-off-in'))
                                        ->icon('heroicon-o-clock')
                                        ->formatStateUsing(fn ($state) => Enums\RequestUnit::options()[$state])
                                        ->placeholder('—')
                                        ->badge(),
                                    Infolists\Components\IconEntry::make('include_public_holidays_in_duration')
                                        ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.public-holiday-included'))
                                        ->boolean()
                                        ->placeholder('—'),
                                    Infolists\Components\IconEntry::make('support_document')
                                        ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.allow-to-attach-supporting-document'))
                                        ->boolean()
                                        ->placeholder('—'),
                                    Infolists\Components\IconEntry::make('show_on_dashboard')
                                        ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.show-on-dashboard'))
                                        ->boolean()
                                        ->placeholder('—'),
                                    Infolists\Components\TextEntry::make('time_type')
                                        ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.kind-of-time'))
                                        ->icon('heroicon-o-clock')
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state) => Enums\TimeType::options()[$state])
                                        ->badge(),
                                    Infolists\Components\IconEntry::make('allows_negative')
                                        ->boolean()
                                        ->visible(fn ($record) => $record->requires_allocation === Enums\RequiresAllocation::YES->value)
                                        ->placeholder('—'),
                                    Infolists\Components\TextEntry::make('max_allowed_negative')
                                        ->label(__('time_off::filament/clusters/configurations/resources/leave-type.infolist.sections.configuration.entries.max-negative-cap'))
                                        ->icon('heroicon-o-arrow-trending-down')
                                        ->placeholder('—')
                                        ->visible(fn ($record) => $record->requires_allocation === Enums\RequiresAllocation::YES->value && $record->allows_negative === true)
                                        ->numeric(),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'view'   => Pages\ViewLeaveType::route('/{record}'),
            'edit'   => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }
}
