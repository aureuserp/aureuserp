<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\ProjectStages;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ProjectStages\Pages\ManageProjectStages;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ProjectStages\Schemas\ProjectStageForm;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ProjectStages\Tables\ProjectStagesTable;
use Webkul\Project\Models\ProjectStage;
use Webkul\Project\Settings\TaskSettings;

class ProjectStageResource extends Resource
{
    protected static ?string $model = ProjectStage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/project-stage.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(TaskSettings::class)->enable_project_stages;
    }

    public static function form(Schema $schema): Schema
    {
        return ProjectStageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectStagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProjectStages::route('/'),
        ];
    }
}
