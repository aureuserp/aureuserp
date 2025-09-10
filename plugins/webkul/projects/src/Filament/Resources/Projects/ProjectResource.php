<?php

namespace Webkul\Project\Filament\Resources\Projects;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Project\Filament\Resources\Projects\Pages\CreateProject;
use Webkul\Project\Filament\Resources\Projects\Pages\EditProject;
use Webkul\Project\Filament\Resources\Projects\Pages\ListProjects;
use Webkul\Project\Filament\Resources\Projects\Pages\ManageMilestones;
use Webkul\Project\Filament\Resources\Projects\Pages\ManageTasks;
use Webkul\Project\Filament\Resources\Projects\Pages\ViewProject;
use Webkul\Project\Filament\Resources\Projects\RelationManagers\MilestonesRelationManager;
use Webkul\Project\Filament\Resources\Projects\RelationManagers\TaskStagesRelationManager;
use Webkul\Project\Filament\Resources\Projects\Schemas\ProjectForm;
use Webkul\Project\Filament\Resources\Projects\Schemas\ProjectInfolist;
use Webkul\Project\Filament\Resources\Projects\Tables\ProjectsTable;
use Webkul\Project\Models\Project;

class ProjectResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Project::class;

    protected static ?string $slug = 'project/projects';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/project.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('projects::filament/resources/project.navigation.group');
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
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectInfolist::configure($schema);
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
