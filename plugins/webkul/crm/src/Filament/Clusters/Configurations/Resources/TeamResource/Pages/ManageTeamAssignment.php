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

class ManageTeamAssignment extends EditRecord
{
    use HasCRMClusterNavigation, HasRecordNavigationTabs, ManagesCRMTeam {
        HasCRMClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCRMClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = TeamResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function canAccess(array $parameters = []): bool
    {
        return TeamResource::usesRuleBasedAssignment() && parent::canAccess($parameters);
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.pages.assignment.navigation');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.pages.assignment.title', ['team' => $this->getRecord()->name]);
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.pages.assignment.navigation');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TeamForm::getAssignmentSection()
                    ->columnSpanFull(),
            ]);
    }
}
