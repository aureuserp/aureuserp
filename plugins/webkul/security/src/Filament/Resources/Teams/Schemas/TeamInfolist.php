<?php

namespace Webkul\Security\Filament\Resources\Teams\Schemas;

use Filament\Infolists\Components\TextEntry;

class TeamInfolist
{
    public static function configure($schema)
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->icon('heroicon-o-user')
                    ->placeholder('—')
                    ->label(__('security::filament/resources/team.infolist.entries.name')),
            ]);
    }
}
