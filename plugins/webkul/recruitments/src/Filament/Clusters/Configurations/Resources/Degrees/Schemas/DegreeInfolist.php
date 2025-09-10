<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Degrees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DegreeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.infolist.name')),
            ]);
    }
}
