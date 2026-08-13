<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Schemas\ActivityTemplateForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Schemas\ActivityTemplateInfolist;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Tables\ActivityTemplatesTable;

class ActivityTemplateRelationManager extends RelationManager
{
    protected static string $relationship = 'activityPlanTemplates';

    public function form(Schema $schema): Schema
    {
        return ActivityTemplateForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ActivityTemplatesTable::configure($table);
    }

    public function infolist(Schema $schema): Schema
    {
        return ActivityTemplateInfolist::configure($schema);
    }
}
