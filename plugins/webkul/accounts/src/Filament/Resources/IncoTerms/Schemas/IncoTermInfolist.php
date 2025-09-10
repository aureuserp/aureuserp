<?php

namespace Webkul\Account\Filament\Resources\IncoTerms\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class IncotermInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')
                    ->placeholder(__('accounts::filament/resources/incoterm.infolist.entries.code')),
                TextEntry::make('name')
                    ->placeholder(__('accounts::filament/resources/incoterm.infolist.entries.name')),
            ]);
    }
}
