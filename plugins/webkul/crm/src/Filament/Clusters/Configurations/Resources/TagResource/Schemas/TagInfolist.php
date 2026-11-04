<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TagInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/tag.infolist.entries.name'))
                    ->placeholder('—'),
                ColorEntry::make('color')
                    ->label(__('crm::filament/clusters/configurations/resources/tag.infolist.entries.color'))
                    ->placeholder('—'),
            ])
            ->columns(1);
    }
}
