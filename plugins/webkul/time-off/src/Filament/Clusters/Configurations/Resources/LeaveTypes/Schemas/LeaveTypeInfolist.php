<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Webkul\TimeOff\Enums\AllocationValidationType;
use Webkul\TimeOff\Enums\EmployeeRequest;
use Webkul\TimeOff\Enums\RequestUnit;
use Webkul\TimeOff\Enums\RequiresAllocation;
use Webkul\TimeOff\Enums\TimeType;

class LeaveTypeInfolist
{
    public static function configure(Schema $schema): Schema
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
                        ]),
                    ])->columnSpanFull(),
            ]);
    }
}
