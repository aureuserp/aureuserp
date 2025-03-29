<?php

namespace Webkul\TableViews\Filament\Components;

use Webkul\TableViews\Models\TableView;

class SavedView extends PresetView
{
    protected TableView $model;

    public function model(TableView $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): TableView
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
        return $this->model->is_public;
    }

    public function isEditable(): bool
    {
        return $this->model->user_id === auth()->id();
    }

    public function isReplaceable(): bool
    {
        return $this->model->user_id === auth()->id();
    }

    public function isDeletable(): bool
    {
        return $this->model->user_id === auth()->id();
    }

    public function getVisibilityIcon(): string
    {
        return $this->isPublic() ? 'heroicon-o-eye' : 'heroicon-o-user';
    }
}
