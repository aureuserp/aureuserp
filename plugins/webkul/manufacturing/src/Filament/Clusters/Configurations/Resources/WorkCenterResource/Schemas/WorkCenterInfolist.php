<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn as InfolistTableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset as InfolistFieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Manufacturing\Models\WorkCenter;

class WorkCenterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-cog-6-tooth')
                                    ->columnSpanFull(),
                                TextEntry::make('code')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.code'))
                                    ->placeholder('—'),
                                TextEntry::make('working_state')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.working-state'))
                                    ->badge(),
                                TextEntry::make('tags.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.tags'))
                                    ->badge()
                                    ->separator(',')
                                    ->placeholder('—'),
                                TextEntry::make('alternativeWorkCenters.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.alternative-work-centers'))
                                    ->listWithLineBreaks()
                                    ->placeholder('—'),
                                TextEntry::make('calendar.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.calendar'))
                                    ->placeholder('—'),
                                TextEntry::make('company.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.general.entries.company'))
                                    ->placeholder('—'),
                            ])
                            ->columns(2),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.description.title'))
                            ->schema([
                                TextEntry::make('note')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.description.entries.note'))
                                    ->placeholder('—'),
                            ])
                            ->visible(fn (WorkCenter $record): bool => filled($record->note)),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.title'))
                            ->schema([
                                RepeatableEntry::make('capacities')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.product')),
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.product-uom')),
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.capacity')),
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.setup-time')),
                                        InfolistTableColumn::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.specific-capacity.columns.cleanup-time')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('product.uom.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('capacity')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 1), 2))
                                            ->placeholder('—'),
                                        TextEntry::make('time_start')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 0, 'minutes'))
                                            ->placeholder('—'),
                                        TextEntry::make('time_stop')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 0, 'minutes'))
                                            ->placeholder('—'),
                                    ]),
                            ])
                            ->visible(fn (WorkCenter $record): bool => $record->capacities()->exists()),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.title'))
                            ->schema([
                                InfolistFieldset::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.fieldsets.production-information'))
                                    ->schema([
                                        TextEntry::make('time_efficiency')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.time-efficiency'))
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 100), 2).' %')
                                            ->placeholder('—'),
                                        TextEntry::make('default_capacity')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.default-capacity'))
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 1), 2))
                                            ->placeholder('—'),
                                        TextEntry::make('oee_target')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.oee-target'))
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 90), 2).' %')
                                            ->placeholder('—'),
                                        TextEntry::make('setup_time')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.setup-time'))
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 0, 'minutes').' '.__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.time-suffix'))
                                            ->placeholder('—'),
                                        TextEntry::make('cleanup_time')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.cleanup-time'))
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 0, 'minutes').' '.__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.time-suffix'))
                                            ->placeholder('—'),
                                    ])
                                    ->columns(1),

                                InfolistFieldset::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.fieldsets.costing-information'))
                                    ->schema([
                                        TextEntry::make('costs_per_hour')
                                            ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.costs-per-hour'))
                                            ->formatStateUsing(fn (mixed $state): string => number_format((float) ($state ?? 0), 2).' '.__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.information.entries.cost-suffix'))
                                            ->placeholder('—'),
                                    ])
                                    ->columns(1),
                            ]),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.record-information.entries.created-by'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('created_at')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('updated_at')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-clock'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
