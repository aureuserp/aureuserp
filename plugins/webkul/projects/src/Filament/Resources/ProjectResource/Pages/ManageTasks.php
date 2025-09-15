<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Task;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageTasks extends ManageRelatedRecords
{
    use HasTableViews;

    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'tasks';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/project/pages/manage-tasks.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('projects::filament/resources/project/pages/manage-tasks.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->url(TaskResource::getUrl('create')),
        ];
    }

    public function table(Table $table): Table
    {
        return TaskResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (Task $record): string => TaskResource::getUrl('view', ['record' => $record]))
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->url(fn (Task $record): string => TaskResource::getUrl('edit', ['record' => $record]))
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project/pages/manage-tasks.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/project/pages/manage-tasks.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project/pages/manage-tasks.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/project/pages/manage-tasks.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project/pages/manage-tasks.table.actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/project/pages/manage-tasks.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereNull('parent_id');
            });
    }

    public function infolist(Schema $schema): Schema
    {
        return TaskResource::infolist($schema);
    }

    public function getPresetTableViews(): array
    {
        return [
            'open_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.open-tasks'))
                ->icon('heroicon-s-bolt')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('state', [
                    TaskState::CANCELLED,
                    TaskState::DONE,
                ])),

            'my_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.my-tasks'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereHas('users', function ($q) {
                            $q->where('user_id', Auth::id());
                        });
                }),

            'unassigned_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.unassigned-tasks'))
                ->icon('heroicon-s-user-minus')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereDoesntHave('users');
                }),

            'closed_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.closed-tasks'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    TaskState::CANCELLED,
                    TaskState::DONE,
                ])),

            'starred_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.starred-tasks'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('priority', true)),

            'archived_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.archived-tasks'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
