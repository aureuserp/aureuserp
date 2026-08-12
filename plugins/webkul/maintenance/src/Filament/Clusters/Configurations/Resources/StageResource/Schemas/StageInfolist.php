<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('maintenance::filament/clusters/configurations/resources/stage.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/stage.infolist.sections.general.entries.name')),

                        IconEntry::make('done')
                            ->label(__('maintenance::filament/clusters/configurations/resources/stage.infolist.sections.general.entries.done'))
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }
}
