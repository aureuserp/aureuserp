<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Crm\Filament\Support\ProbabilityHint;
use Webkul\Crm\Models\Lead;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('crm::filament/clusters/leads/resources/lead.infolist.sections.lost'))
                            ->icon('heroicon-o-hand-thumb-down')
                            ->visible(fn (Lead $record): bool => $record->trashed())
                            ->schema([
                                TextEntry::make('lostReason.name')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.infolist.entries.lost-reason'))
                                    ->placeholder('—'),
                                TextEntry::make('lost_feedback')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.infolist.entries.closing-note'))
                                    ->placeholder('—'),
                            ])
                            ->columns(2),
                        Section::make(__('crm::filament/clusters/leads/resources/lead.form.sections.general'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.name'))
                                    ->hiddenLabel()
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('probability')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.probability'))
                                    ->numeric(decimalPlaces: 2)
                                    ->suffix('%')
                                    ->afterLabel([ProbabilityHint::component(), ProbabilityHint::viewResetAction()]),
                                Grid::make(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('company_name')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.company-name'))
                                                    ->placeholder('—'),
                                                TextEntry::make('address')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.address'))
                                                    ->state(fn (Lead $record): ?string => collect([
                                                        $record->street1,
                                                        $record->street2,
                                                        collect([$record->city, $record->state?->name, $record->zip])->filter()->implode(', '),
                                                        $record->country?->name,
                                                    ])->filter()->map(fn (string $line): string => e($line))->implode('<br>') ?: null)
                                                    ->html()
                                                    ->placeholder('—'),
                                                TextEntry::make('website')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.website'))
                                                    ->url(fn (Lead $record): ?string => $record->website, shouldOpenInNewTab: true)
                                                    ->placeholder('—'),
                                            ]),
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('contact_name')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.contact-name'))
                                                    ->placeholder('—'),
                                                TextEntry::make('email')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.email'))
                                                    ->placeholder('—'),
                                                TextEntry::make('job_title')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.job-position'))
                                                    ->placeholder('—'),
                                                TextEntry::make('phone')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.phone'))
                                                    ->placeholder('—'),
                                            ]),
                                    ]),
                            ]),
                        Section::make(__('crm::filament/clusters/leads/resources/lead.form.tabs.notes'))
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
                        Section::make(__('crm::filament/clusters/leads/resources/lead.form.sections.sales'))
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.salesperson'))
                                    ->placeholder('—'),
                                TextEntry::make('team.name')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.team'))
                                    ->placeholder('—'),
                                TextEntry::make('priority')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.priority'))
                                    ->badge(),
                                TextEntry::make('tags.name')
                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.tags'))
                                    ->badge()
                                    ->placeholder('—'),
                            ]),
                        Section::make(__('crm::filament/clusters/leads/resources/lead.form.fieldsets.marketing'))
                            ->schema([
                                TextEntry::make('company.name')->label(__('crm::filament/clusters/leads/resources/lead.form.fields.company'))->placeholder('—'),
                                TextEntry::make('campaign.name')->label(__('crm::filament/clusters/leads/resources/lead.form.fields.campaign'))->placeholder('—'),
                                TextEntry::make('medium.name')->label(__('crm::filament/clusters/leads/resources/lead.form.fields.medium'))->placeholder('—'),
                                TextEntry::make('source.name')->label(__('crm::filament/clusters/leads/resources/lead.form.fields.source'))->placeholder('—'),
                                TextEntry::make('referred_by')->label(__('crm::filament/clusters/leads/resources/lead.form.fields.referred-by'))->placeholder('—'),
                                TextEntry::make('reveal_ip')->label(__('crm::visits-to-leads.ip-address'))->visible(fn ($record): bool => filled($record?->reveal_ip)),
                            ]),
                        Section::make(__('crm::filament/clusters/leads/resources/lead.form.fieldsets.analysis'))
                            ->schema([
                                TextEntry::make('assigned_at')->label(__('crm::filament/clusters/leads/resources/lead.form.fields.assignment-date'))->dateTime()->placeholder('—'),
                                TextEntry::make('closed_at')->label(__('crm::filament/clusters/leads/resources/lead.form.fields.closed-date'))->dateTime()->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
