<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Livewire;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Schemas\ActivityTemplateForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Schemas\ActivityTemplateInfolist;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers\ActivityTemplateRelationManager\Tables\ActivityTemplatesTable;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageActivityPlanTemplates extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = ActivityPlanResource::class;

    protected static string $relationship = 'activityPlanTemplates';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('Activity Plan Templates');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return (string) Livewire::current()->getRecord()->activityPlanTemplates()->count();
    }

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
