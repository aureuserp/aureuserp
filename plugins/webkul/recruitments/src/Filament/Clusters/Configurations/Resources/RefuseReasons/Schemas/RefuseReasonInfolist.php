<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasons\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RefuseReasonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.infolist.name')),
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.infolist.template')),
            ]);
    }
}
