<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EquipmentInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/resources/equipment.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.general.entries.name')),

                                TextEntry::make('note')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.general.entries.note'))
                                    ->placeholder('—'),
                            ]),

                        Section::make(__('maintenance::filament/resources/equipment.infolist.sections.product-information.title'))
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.partner'))
                                    ->placeholder('—'),

                                TextEntry::make('partner_ref')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.partner-ref'))
                                    ->placeholder('—'),

                                TextEntry::make('model')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.model'))
                                    ->placeholder('—'),

                                TextEntry::make('serial_no')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.serial-no'))
                                    ->placeholder('—'),

                                TextEntry::make('effective_date')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.effective-date'))
                                    ->date()
                                    ->placeholder('—'),

                                TextEntry::make('cost')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.cost'))
                                    ->numeric(decimalPlaces: 2)
                                    ->placeholder('—'),

                                TextEntry::make('warranty_date')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.warranty-date'))
                                    ->date()
                                    ->placeholder('—'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/resources/equipment.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('category.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.category'))
                                    ->placeholder('—'),

                                TextEntry::make('team.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.team'))
                                    ->placeholder('—'),

                                TextEntry::make('company.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.company'))
                                    ->placeholder('—'),

                                TextEntry::make('technician.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.technician'))
                                    ->placeholder('—'),

                                TextEntry::make('owner.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.owner'))
                                    ->placeholder('—'),

                                TextEntry::make('location')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.location'))
                                    ->placeholder('—'),
                            ]),

                        Section::make(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.title'))
                            ->schema([
                                TextEntry::make('expected_mtbf')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.expected-mtbf'))
                                    ->suffix(' '.__('maintenance::filament/resources/equipment.infolist.sections.maintenance.suffixes.days'))
                                    ->placeholder('0'),

                                TextEntry::make('maintenance_count')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.maintenance-count'))
                                    ->placeholder('0'),

                                TextEntry::make('maintenance_open_count')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.maintenance-open-count'))
                                    ->placeholder('0'),

                                TextEntry::make('assigned_at')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.assigned-at'))
                                    ->date()
                                    ->placeholder('—'),

                                TextEntry::make('scraped_at')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.scraped-at'))
                                    ->date()
                                    ->placeholder('—'),

                                ...$customInfolistEntries,
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
