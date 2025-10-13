<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource;

class ListStages extends ListRecords
{
    protected static string $resource = StageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('recruitments::filament/clusters/configurations/resources/stage/pages/list-stage.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function ($data) {
                    return $data;
                }),
        ];
    }
}
