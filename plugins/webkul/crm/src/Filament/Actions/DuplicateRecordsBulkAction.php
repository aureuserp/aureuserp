<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DuplicateRecordsBulkAction extends BulkAction
{
    protected array $relationsToCopy = [];

    public static function getDefaultName(): ?string
    {
        return 'duplicateRecords';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.duplicate.label'))
            ->icon('heroicon-o-document-duplicate')
            ->visible(fn (mixed $livewire): bool => ! method_exists($livewire, 'getResource') || $livewire::getResource()::canCreate())
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $records->each(fn (Model $record): Model => $this->duplicate($record));

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.duplicate.notification', ['count' => $records->count()]))
                    ->send();
            });
    }

    public function copyRelations(array $relations): static
    {
        $this->relationsToCopy = $relations;

        return $this;
    }

    public function duplicate(Model $record): Model
    {
        $record = $record->fresh() ?? $record;

        $copy = $record->replicate(['deleted_at']);
        $copy->name = $this->copyName($record);
        $copy->save();

        foreach ($this->relationsToCopy as $name) {
            $relation = $record->{$name}();

            if ($relation instanceof BelongsToMany) {
                $copy->{$name}()->sync($relation->allRelatedIds());

                continue;
            }

            if ($relation instanceof HasMany) {
                $relation->get()->each(fn (Model $child): bool => $child->replicate()
                    ->setAttribute($relation->getForeignKeyName(), $copy->getKey())
                    ->save());
            }
        }

        return $copy;
    }

    protected function copyName(Model $record): string
    {
        $name = __('crm::filament/lead-actions.duplicate.copy', ['name' => $record->name]);
        $candidate = $name;
        $number = 2;

        while ($record->newQueryWithoutScopes()->where('name', $candidate)->exists()) {
            $candidate = $name.' '.$number++;
        }

        return $candidate;
    }
}
