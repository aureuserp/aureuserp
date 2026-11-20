<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Schemas\ActivityTemplateForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Schemas\ActivityTemplateInfolist;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Tables\ActivityTemplatesTable;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageActivities extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = ActivityPlanResource::class;

    protected static string $relationship = 'activityPlanTemplates';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    public function getTitle(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-plan/pages/manage-activities.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-plan/pages/manage-activities.title');
    }

    public function form(Schema $schema): Schema
    {
        return ActivityTemplateForm::configure($schema);
    }

    public function infolist(Schema $schema): Schema
    {
        return ActivityTemplateInfolist::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ActivityTemplatesTable::configure($table);
    }
}
