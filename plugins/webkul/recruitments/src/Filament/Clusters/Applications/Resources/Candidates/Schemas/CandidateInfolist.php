<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Webkul\Recruitment\Models\Candidate;

class CandidateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.full-name')),
                                        TextEntry::make('partner.name')
                                            ->icon('heroicon-o-identification')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.contact')),
                                        TextEntry::make('email_from')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.email')),
                                        TextEntry::make('phone')
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.phone')),
                                        TextEntry::make('linkedin_profile')
                                            ->icon('heroicon-o-link')
                                            ->placeholder('—')
                                            ->url(fn ($record) => $record->linkedin_profile)
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.basic-information.entries.linkedin')),
                                    ])
                                    ->columns(2),
                                Section::make(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.title'))
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->icon('heroicon-o-building-office')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.company')),
                                        TextEntry::make('degree.name')
                                            ->icon('heroicon-o-academic-cap')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.degree')),
                                        TextEntry::make('categories.name')
                                            ->icon('heroicon-o-tag')
                                            ->placeholder('—')
                                            ->state(function (Candidate $record): array {
                                                return $record->categories->map(fn ($category) => [
                                                    'label' => $category->name,
                                                    'color' => $category->color ?? '#808080',
                                                ])->toArray();
                                            })
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => $state['label'])
                                            ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                            ->listWithLineBreaks()
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.tags')),
                                        TextEntry::make('manager.name')
                                            ->icon('heroicon-o-user-circle')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.manager')),
                                        TextEntry::make('availability_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->date()
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.additional-details.entries.availability-date')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.status-and-evaluation.title'))
                                    ->schema([
                                        IconEntry::make('is_active')
                                            ->boolean()
                                            ->label(__('Status')),
                                        TextEntry::make('priority')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.status-and-evaluation.entries.evaluation'))
                                            ->formatStateUsing(function ($state) {
                                                $html = '<div class="flex gap-1" style="color: rgb(217 119 6);">';
                                                for ($i = 1; $i <= 3; $i++) {
                                                    $iconType = $i <= $state ? 'heroicon-s-star' : 'heroicon-o-star';
                                                    $html .= view('filament::components.icon', [
                                                        'icon'  => $iconType,
                                                        'class' => 'w-5 h-5',
                                                    ])->render();
                                                }

                                                $html .= '</div>';

                                                return new HtmlString($html);
                                            })
                                            ->placeholder('—'),
                                    ]),
                                Section::make(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.communication.title'))
                                    ->schema([
                                        TextEntry::make('email_cc')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.communication.entries.cc-email')),
                                        IconEntry::make('message_bounced')
                                            ->boolean()
                                            ->label(__('recruitments::filament/clusters/applications/resources/candidate.infolist.sections.communication.entries.email-bounced')),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }
}
