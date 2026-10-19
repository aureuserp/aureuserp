<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
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
                                            ->numeric(decimalPlaces: 0)
                                            ->suffix('%'),
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
