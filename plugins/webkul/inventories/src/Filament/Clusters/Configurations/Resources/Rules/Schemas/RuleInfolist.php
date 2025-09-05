<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Rules\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\HtmlString;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Models\Rule;

class RuleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.title'))
                            ->description(function (Rule $record) {
                                $operation = $record->operationType;

                                $pullMessage = __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.description.pull', [
                                    'sourceLocation'      => $operation?->sourceLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.destination-location'),
                                    'operation'           => $operation?->name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.operation-type'),
                                    'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.source-location'),
                                ]);

                                $pushMessage = __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.description.push', [
                                    'sourceLocation'      => $operation?->sourceLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.source-location'),
                                    'operation'           => $operation?->name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.operation-type'),
                                    'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.destination-location'),
                                ]);

                                return match ($record->action) {
                                    RuleAction::PULL      => new HtmlString($pullMessage),
                                    RuleAction::PUSH      => new HtmlString($pushMessage),
                                    RuleAction::PULL_PUSH => new HtmlString($pullMessage.'</br></br>'.$pushMessage),
                                };
                            })
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.name'))
                                                    ->icon('heroicon-o-document-text')
                                                    ->size(TextSize::Large)
                                                    ->weight(FontWeight::Bold),

                                                TextEntry::make('action')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.action'))
                                                    ->icon('heroicon-o-arrows-right-left')
                                                    ->badge(),

                                                TextEntry::make('operationType.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.operation-type'))
                                                    ->icon('heroicon-o-briefcase'),

                                                TextEntry::make('sourceLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.source-location'))
                                                    ->icon('heroicon-o-map-pin'),

                                                TextEntry::make('destinationLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.destination-location'))
                                                    ->icon('heroicon-o-map-pin'),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                TextEntry::make('route.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.route'))
                                                    ->icon('heroicon-o-globe-alt'),

                                                TextEntry::make('company.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.company'))
                                                    ->icon('heroicon-o-building-office'),

                                                TextEntry::make('partner_address_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.partner-address'))
                                                    ->icon('heroicon-o-user-group')
                                                    ->getStateUsing(fn ($record) => $record->partnerAddress?->name)
                                                    ->placeholder('—'),

                                                TextEntry::make('delay')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.lead-time'))
                                                    ->icon('heroicon-o-clock')
                                                    ->suffix(' days')
                                                    ->placeholder('0'),
                                            ]),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
