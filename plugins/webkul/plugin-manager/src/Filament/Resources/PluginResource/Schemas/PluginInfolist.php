<?php

namespace Webkul\PluginManager\Filament\Resources\PluginResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\PluginManager\Filament\Resources\PluginResource;

class PluginInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('plugin-manager::filament/resources/plugin.infolist.section.plugin'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.name'))
                                ->formatStateUsing(fn ($state) => PluginResource::localize('names', $state, ucfirst($state)))
                                ->weight('bold')
                                ->size('lg'),

                            TextEntry::make('latest_version')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.version'))
                                ->badge()
                                ->color('info'),
                        ]),

                    Grid::make(2)
                        ->schema([
                            IconEntry::make('is_installed')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.is_installed'))
                                ->boolean()
                                ->trueIcon('heroicon-s-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('gray'),

                            TextEntry::make('author')
                                ->label(__('plugin-manager::filament/resources/plugin.infolist.author'))
                                ->badge(),
                        ]),

                    TextEntry::make('license')
                        ->label(__('plugin-manager::filament/resources/plugin.infolist.license'))
                        ->default('MIT')
                        ->badge()
                        ->color('success'),

                    TextEntry::make('summary')
                        ->label(__('plugin-manager::filament/resources/plugin.infolist.summary'))
                        ->formatStateUsing(fn ($state, $record) => PluginResource::localize('summaries', $record->name, $state))
                        ->columnSpanFull(),
                ]),

            Group::make([
                Section::make(__('plugin-manager::filament/resources/plugin.infolist.section.dependencies'))
                    ->schema([
                        PluginResource::repeatableEntry('dependencies', 'warning', 'dependencies-repeater'),
                        PluginResource::repeatableEntry('dependents', 'info', 'dependents-repeater'),
                    ]),
            ])->columnSpanFull(),
        ]);
    }
}
