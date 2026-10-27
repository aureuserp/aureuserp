<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

trait ManagesKanbanColumns
{
    abstract public function canAddKanbanColumn(): bool;

    abstract public function getAddKanbanColumnLabel(): string;

    abstract protected function kanbanColumnExists(string $name, ?Model $except = null): bool;

    abstract protected function createKanbanColumn(string $name, array $attributes = []): Model;

    abstract protected function findKanbanColumnRecord(string $value): ?Model;

    abstract protected function canEditKanbanColumnRecord(Model $record): bool;

    abstract protected function canDeleteKanbanColumnRecord(Model $record): bool;

    abstract protected function getKanbanColumnFormComponents(): array;

    abstract protected function countKanbanColumnRecords(Model $record): int;

    public function addKanbanColumn(mixed $name): ?string
    {
        return $this->storeKanbanColumn($name);
    }

    protected function storeKanbanColumn(mixed $name, array $attributes = []): ?string
    {
        abort_unless($this->canAddKanbanColumn(), 403);

        $name = trim((string) $name);

        if (! $this->isValidKanbanColumnName($name)) {
            return null;
        }

        $column = $this->createKanbanColumn($name, Arr::except($attributes, ['name']));

        Notification::make()
            ->success()
            ->title(__('crm::filament/kanban.add-column.created', ['name' => $name]))
            ->send();

        return (string) $column->getKey();
    }

    public function addKanbanColumnAction(): Action
    {
        return Action::make('addKanbanColumn')
            ->label(fn (): string => $this->getAddKanbanColumnLabel())
            ->icon('heroicon-o-plus')
            ->modalHeading(fn (): string => $this->getAddKanbanColumnLabel())
            ->modalSubmitActionLabel(__('crm::filament/kanban.add-column.add'))
            ->modalWidth('md')
            ->authorize(fn (): bool => $this->canAddKanbanColumn())
            ->schema(fn (): array => $this->getKanbanColumnFormComponents())
            ->action(function (array $data, Action $action): void {
                $column = $this->storeKanbanColumn($data['name'] ?? '', $data);

                if (! $column) {
                    $action->halt();
                }

                $this->js('window.crmHighlightKanbanColumn?.('.json_encode($column).')');
            });
    }

    public function canEditKanbanColumn(string $value): bool
    {
        $record = $this->findKanbanColumnRecord($value);

        return $record !== null && $this->canEditKanbanColumnRecord($record);
    }

    public function canDeleteKanbanColumn(string $value): bool
    {
        $record = $this->findKanbanColumnRecord($value);

        return $record !== null && $this->canDeleteKanbanColumnRecord($record);
    }

    public function editKanbanColumnAction(): Action
    {
        return Action::make('editKanbanColumn')
            ->label(__('crm::filament/kanban.column-actions.edit'))
            ->icon('heroicon-o-pencil-square')
            ->modalHeading(fn (array $arguments): string => __('crm::filament/kanban.column-actions.edit-heading', [
                'name' => $this->findKanbanColumnRecord((string) ($arguments['column'] ?? ''))?->getAttribute('name'),
            ]))
            ->modalWidth('md')
            ->schema(fn (): array => $this->getKanbanColumnFormComponents())
            ->fillForm(fn (array $arguments): array => $this->findKanbanColumnRecord((string) ($arguments['column'] ?? ''))?->attributesToArray() ?? [])
            ->action(function (array $data, array $arguments, Action $action): void {
                $record = $this->findKanbanColumnRecord((string) ($arguments['column'] ?? ''));

                abort_unless($record && $this->canEditKanbanColumnRecord($record), 403);

                $data['name'] = trim((string) ($data['name'] ?? ''));

                if (! $this->isValidKanbanColumnName($data['name'], $record)) {
                    $action->halt();
                }

                $record->update($data);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/kanban.column-actions.saved', ['name' => $data['name']]))
                    ->send();
            });
    }

    public function deleteKanbanColumnAction(): Action
    {
        return Action::make('deleteKanbanColumn')
            ->label(__('crm::filament/kanban.column-actions.delete'))
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments): string => __('crm::filament/kanban.column-actions.delete-heading', [
                'name' => $this->findKanbanColumnRecord((string) ($arguments['column'] ?? ''))?->getAttribute('name'),
            ]))
            ->modalDescription(__('crm::filament/kanban.column-actions.delete-description'))
            ->action(function (array $arguments): void {
                $record = $this->findKanbanColumnRecord((string) ($arguments['column'] ?? ''));

                abort_unless($record && $this->canDeleteKanbanColumnRecord($record), 403);

                $name = (string) $record->getAttribute('name');

                if (($count = $this->countKanbanColumnRecords($record)) > 0) {
                    Notification::make()
                        ->danger()
                        ->title(__('crm::filament/kanban.column-actions.in-use.title', ['name' => $name]))
                        ->body(trans_choice('crm::filament/kanban.column-actions.in-use.body', $count, ['count' => $count]))
                        ->persistent()
                        ->send();

                    return;
                }

                $record->delete();

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/kanban.column-actions.deleted', ['name' => $name]))
                    ->send();
            });
    }

    protected function isValidKanbanColumnName(string $name, ?Model $except = null): bool
    {
        $validator = Validator::make(['name' => $name], ['name' => ['required', 'string', 'max:255']]);

        if ($validator->fails()) {
            Notification::make()
                ->danger()
                ->title($validator->errors()->first('name'))
                ->send();

            return false;
        }

        if ($this->kanbanColumnExists($name, $except)) {
            Notification::make()
                ->warning()
                ->title(__('crm::filament/kanban.add-column.exists', ['name' => $name]))
                ->send();

            return false;
        }

        return true;
    }
}
