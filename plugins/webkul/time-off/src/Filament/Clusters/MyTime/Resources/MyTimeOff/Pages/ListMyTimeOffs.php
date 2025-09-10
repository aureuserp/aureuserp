<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\MyTimeOffResource;

class ListMyTimeOffs extends ListRecords
{
    protected static string $resource = MyTimeOffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
