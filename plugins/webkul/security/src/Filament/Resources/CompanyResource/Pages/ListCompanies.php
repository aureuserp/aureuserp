<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Security\Filament\Resources\CompanyResource;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('security::filament/resources/company/pages/list-company.tabs.all'))
                ->badge(fn() => $this->getResource()::getEloquentQuery()->count())
                ->modifyQueryUsing(fn($query) => $query->whereNull('parent_id')),

            'archived' => Tab::make(__('security::filament/resources/company/pages/list-company.tabs.archived'))
                ->badge(fn() => $this->getResource()::getEloquentQuery()->onlyTrashed()->count())
                ->modifyQueryUsing(fn($query) => $query->onlyTrashed()),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon('heroicon-o-plus-circle')
                ->label(__('security::filament/resources/company/pages/list-company.header-actions.create.label')),
        ];
    }
}
