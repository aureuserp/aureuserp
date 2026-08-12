<?php

namespace Webkul\PluginManager\Filament\Resources\PluginResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\PluginManager\Filament\Resources\PluginResource;
use Webkul\PluginManager\Package;

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
                        static::repeatableEntry('dependencies', 'warning', 'dependencies-repeater'),
                        static::repeatableEntry('dependents', 'info', 'dependents-repeater'),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    private static function repeatableEntry(string $type, string $color, string $key): RepeatableEntry
    {
        return RepeatableEntry::make($type)
            ->label(__('plugin-manager::filament/resources/plugin.infolist.'.$key.'.title'))
            ->state(function ($record) use ($type) {
                return collect($record->{'get'.ucfirst($type).'FromConfig'}())->map(fn ($dep) => [
                    'name'         => $dep,
                    'is_installed' => Package::isPluginInstalled($dep),
                ]);
            })
            ->schema([
                TextEntry::make('name')
                    ->label(__('plugin-manager::filament/resources/plugin.infolist.'.$key.'.name'))
                    ->formatStateUsing(fn ($state) => PluginResource::localize('names', $state, ucfirst($state)))
                    ->badge()
                    ->color($color),

                IconEntry::make('is_installed')
                    ->label(__('plugin-manager::filament/resources/plugin.infolist.'.$key.'.is_installed'))
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->columns(2)
            ->placeholder(__('plugin-manager::filament/resources/plugin.infolist.'.$key.'.placeholder'));
    }
}
