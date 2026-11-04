<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use BackedEnum;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Schemas\TeamForm;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\ManagesCRMTeam;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageTeamAlias extends EditRecord
{
    use HasCRMClusterNavigation, HasRecordNavigationTabs, ManagesCRMTeam {
        HasCRMClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCRMClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = TeamResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-at-symbol';

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.pages.alias.navigation');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.pages.alias.title', ['team' => $this->getRecord()->name]);
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.pages.alias.navigation');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TeamForm::getAliasSection()
                    ->columnSpanFull(),
            ]);
    }
}
