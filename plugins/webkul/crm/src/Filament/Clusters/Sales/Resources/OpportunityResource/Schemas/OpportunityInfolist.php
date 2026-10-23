<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource;
use Webkul\Crm\Filament\Support\ProbabilityHint;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;

class OpportunityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('stage_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.stage'))
                    ->hiddenLabel()
                    ->inline()
                    ->extraAttributes(['class' => 'crm-stepper-previous-active'])
                    ->options(fn (): array => Stage::query()->orderBy('sort')->pluck('name', 'id')->all())
                    ->columnSpanFull(),

                Group::make()
                    ->schema([
                        Section::make(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.lost.title'))
                            ->icon('heroicon-o-hand-thumb-down')
                            ->visible(fn (Lead $record): bool => $record->trashed())
                            ->schema([
                                TextEntry::make('lostReason.name')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.lost.entries.lost-reason'))
                                    ->placeholder('—'),
                                TextEntry::make('lost_feedback')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.lost.entries.feedback'))
                                    ->placeholder('—'),
                            ]),

                        Section::make(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.name'))
                                    ->hiddenLabel()
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('expected_revenue')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.general.entries.expected-revenue'))
                                            ->money(fn (Lead $record): ?string => $record->company?->currency?->code),
                                        TextEntry::make('probability')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.general.entries.probability'))
                                            ->numeric(decimalPlaces: 2)
                                            ->suffix('%')
                                            ->afterLabel([ProbabilityHint::component(), ProbabilityHint::viewResetAction()]),
                                        TextEntry::make('recurring_revenue')
                                            ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.opportunity.recurring-revenue'))
                                            ->money(fn (Lead $record): ?string => $record->company?->currency?->code)
                                            ->suffix(fn (Lead $record): ?string => $record->recurringPlan ? ' · '.$record->recurringPlan->name : null)
                                            ->visible(fn (Lead $record): bool => RecurringPlanResource::isRecurringRevenueEnabled() && (float) $record->recurring_revenue > 0),
                                        TextEntry::make('monthly_recurring_revenue')
                                            ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.opportunity.mrr'))
                                            ->state(fn (Lead $record): float => $record->monthlyRecurringRevenue())
                                            ->money(fn (Lead $record): ?string => $record->company?->currency?->code)
                                            ->visible(fn (Lead $record): bool => RecurringPlanResource::isRecurringRevenueEnabled() && (float) $record->recurring_revenue > 0),
                                        TextEntry::make('partner.name')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.general.entries.customer'))
                                            ->placeholder('—'),
                                        TextEntry::make('email')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.general.entries.email'))
                                            ->placeholder('—'),
                                        TextEntry::make('phone')
                                            ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.general.entries.phone'))
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        Section::make(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.notes.title'))
                            ->schema([
                                TextEntry::make('description')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.tabs.notes'))
                                    ->hiddenLabel()
                                    ->html()
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.sales.title'))
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.sales.entries.salesperson'))
                                    ->placeholder('—'),
                                TextEntry::make('team.name')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.sales.entries.team'))
                                    ->placeholder('—'),
                                TextEntry::make('company.name')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.sales.entries.company'))
                                    ->placeholder('—'),
                                TextEntry::make('expected_closing_date')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.sales.entries.expected-closing'))
                                    ->date()
                                    ->placeholder('—'),
                                TextEntry::make('priority')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.sales.entries.priority'))
                                    ->badge(),
                                TextEntry::make('tags.name')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.sales.entries.tags'))
                                    ->badge()
                                    ->placeholder('—'),
                                TextEntry::make('closed_at')
                                    ->label(__('crm::filament/clusters/sales/resources/opportunity.infolist.sections.sales.entries.closed-at'))
                                    ->dateTime()
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
