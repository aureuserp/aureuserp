<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\SaleTeamResource;
use Webkul\Sale\Models\Team;

class ListTeams extends ListRecords
{
    protected static string $resource = SaleTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All'))
                ->badge(Team::count()),
            'archived' => Tab::make(__('Archived'))
                ->badge(Team::onlyTrashed()->count())
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
