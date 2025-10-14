<?php

namespace Webkul\TableViews\Filament\Components;

use Closure;
use Illuminate\Database\Eloquent\Model;

class SavedView extends PresetView
{
    protected Model|array|string|Closure|null $model = null;

    public function model(Model|array|string|Closure|null $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function isFavorite(string|int|null $id = null): bool
    {
        $tableViewFavorite = $this->getCachedFavoriteTableViews()
            ->where('view_type', 'saved')
            ->where('view_key', $id ?? $this->model->id)
            ->first();

        return (bool) ($tableViewFavorite?->is_favorite ?? $this->isFavorite);
    }

    public function isPublic(): bool
    {
        return $this->getRecord()->is_public;
    }

    public function isEditable(): bool
    {
        return $this->getRecord()->user_id === filament()->auth()->id();
    }

    public function isReplaceable(): bool
    {
        return $this->getRecord()->user_id === filament()->auth()->id();
    }

    public function isDeletable(): bool
    {
        return $this->getRecord()->user_id === filament()->auth()->id();
    }

    public function getVisibilityIcon(): string
    {
        return $this->isPublic() ? 'heroicon-o-eye' : 'heroicon-o-user';
    }
}
