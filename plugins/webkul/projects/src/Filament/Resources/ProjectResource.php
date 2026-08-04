<?php

namespace Webkul\Project\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\CreateProject;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\EditProject;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ListProjects;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageMilestones;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ViewProject;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\MilestonesRelationManager;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\TaskStagesRelationManager;
use Webkul\Project\Filament\Resources\ProjectResource\Schemas\ProjectForm;
use Webkul\Project\Filament\Resources\ProjectResource\Schemas\ProjectInfolist;
use Webkul\Project\Filament\Resources\ProjectResource\Tables\ProjectsTable;
use Webkul\Project\Models\Project;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Project\Settings\TimeSettings;
use Webkul\Support\Enums\NavigationGroup;

class ProjectResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Project::class;

    protected static ?string $slug = 'project/projects';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/project.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'user.name', 'partner.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('projects::filament/resources/project.global-search.project-manager') => $record->user?->name ?? '—',
            __('projects::filament/resources/project.global-search.customer')        => $record->partner?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table, static::getCustomTableColumns(), static::getTableQueryBuilderConstraints());
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getTaskSettings(): TaskSettings
    {
        return settings(TaskSettings::class);
    }

    public static function getTimeSettings(): TimeSettings
    {
        return settings(TimeSettings::class);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProject::class,
            EditProject::class,
            ManageTasks::class,
            ManageMilestones::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Task Stages', [
                TaskStagesRelationManager::class,
            ])
                ->icon('heroicon-o-squares-2x2'),

            RelationGroup::make('Milestones', [
                MilestonesRelationManager::class,
            ])
                ->icon('heroicon-o-flag'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListProjects::route('/'),
            'create'     => CreateProject::route('/create'),
            'edit'       => EditProject::route('/{record}/edit'),
            'view'       => ViewProject::route('/{record}'),
            'milestones' => ManageMilestones::route('/{record}/milestones'),
            'tasks'      => ManageTasks::route('/{record}/tasks'),
        ];
    }
}
