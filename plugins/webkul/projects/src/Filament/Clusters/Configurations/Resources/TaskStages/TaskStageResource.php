<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStages;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStages\Pages\ManageTaskStages;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStages\Schemas\TaskStageForm;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStages\Tables\TaskStagesTable;
use Webkul\Project\Models\TaskStage;

class TaskStageResource extends Resource
{
    protected static ?string $model = TaskStage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/task-stage.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return TaskStageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskStagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTaskStages::route('/'),
        ];
    }
}
