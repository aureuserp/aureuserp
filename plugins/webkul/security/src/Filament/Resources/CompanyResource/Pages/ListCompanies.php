<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Support\Models\Company;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListCompanies extends ListRecords
{
    use HasTableViews;

    protected static string $resource = CompanyResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('security::filament/resources/company/pages/list-company.tabs.all'))
                ->badge(Company::count()),
            'archived' => Tab::make(__('security::filament/resources/company/pages/list-company.tabs.archived'))
                ->badge(Company::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
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
