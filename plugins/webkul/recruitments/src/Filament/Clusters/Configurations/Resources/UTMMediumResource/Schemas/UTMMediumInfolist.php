<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UTMMediumInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/utm-medium.infolist.name')),
            ]);
    }
}
