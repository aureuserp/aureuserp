<?php

namespace Webkul\Project\Filament\Resources\Tasks;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Project\Filament\Resources\Tasks\Pages\CreateTask;
use Webkul\Project\Filament\Resources\Tasks\Pages\EditTask;
use Webkul\Project\Filament\Resources\Tasks\Pages\ListTasks;
use Webkul\Project\Filament\Resources\Tasks\Pages\ManageSubTasks;
use Webkul\Project\Filament\Resources\Tasks\Pages\ManageTimesheets;
use Webkul\Project\Filament\Resources\Tasks\Pages\ViewTask;
use Webkul\Project\Filament\Resources\Tasks\RelationManagers\SubTasksRelationManager;
use Webkul\Project\Filament\Resources\Tasks\RelationManagers\TimesheetsRelationManager;
use Webkul\Project\Filament\Resources\Tasks\Schemas\TaskForm;
use Webkul\Project\Filament\Resources\Tasks\Schemas\TaskInfolist;
use Webkul\Project\Filament\Resources\Tasks\Tables\TasksTable;
use Webkul\Project\Models\Task;

class TaskResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Task::class;

    protected static ?string $slug = 'project/tasks';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/task.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('projects::filament/resources/task.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'project.name', 'partner.name', 'milestone.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('projects::filament/resources/task.global-search.project')   => $record->project?->name ?? '—',
            __('projects::filament/resources/task.global-search.customer')  => $record->partner?->name ?? '—',
            __('projects::filament/resources/task.global-search.milestone') => $record->milestone?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaskInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewTask::class,
            EditTask::class,
            ManageTimesheets::class,
            ManageSubTasks::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Timesheets', [
                TimesheetsRelationManager::class,
            ])
                ->icon('heroicon-o-clock'),

            RelationGroup::make('Sub Tasks', [
                SubTasksRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListTasks::route('/'),
            'create'     => CreateTask::route('/create'),
            'edit'       => EditTask::route('/{record}/edit'),
            'view'       => ViewTask::route('/{record}'),
            'timesheets' => ManageTimesheets::route('/{record}/timesheets'),
            'sub-tasks'  => ManageSubTasks::route('/{record}/sub-tasks'),
        ];
    }
}
