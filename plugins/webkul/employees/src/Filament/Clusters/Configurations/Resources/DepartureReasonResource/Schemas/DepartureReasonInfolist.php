<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DepartureReasonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.infolist.name')),
            ]);
    }
}
