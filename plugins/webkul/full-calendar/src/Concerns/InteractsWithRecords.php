<?php

namespace Webkul\FullCalendar\Concerns;

use function Filament\Support\get_model_label;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Locked;

trait InteractsWithRecords
{
    #[Locked]
    public Model | string | null $model = null;

    protected ?string $modelLabel = null;

    #[Locked]
    public Model | int | string | null $record;

    protected static ?string $recordRouteKeyName = null;

    protected function resolveRecord(int | string $key): Model
    {
        $record = $this->resolveRecordRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($this->getModel(), [$key]);
        }

        return $record;
    }

    public function getModel(): ?string
    {
        $model = $this->model;

        if ($model instanceof Model) {
            return $model::class;
        }

        if (filled($model)) {
            return $model;
        }

        return null;
    }

    public function hasRecord(): bool
    {
        return filled($this->getRecord());
    }

    public function getRecord(): ?Model
    {
        $record = $this->record;

        if ($record instanceof Model) {
            return $record;
        }

        if (is_string($record)) {
            return null;
        }

        return null;
    }

    public function resolveRecordRouteBinding(int | string $key): ?Model
    {
        return app($this->getModel())
            ->resolveRouteBindingQuery($this->getEloquentQuery(), $key, $this->getRecordRouteKeyName())
            ->first();
    }

    protected function getEloquentQuery(): Builder
    {
        return app($this->getModel())::query();
    }

    protected function getRecordRouteKeyName(): ?string
    {
        return static::$recordRouteKeyName;
    }

    protected function getModelLabel(): string
    {
        return $this->modelLabel ?? get_model_label($this->getModel());
    }
}
