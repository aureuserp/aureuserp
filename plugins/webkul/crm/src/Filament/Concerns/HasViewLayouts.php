<?php

namespace Webkul\Crm\Filament\Concerns;

use Livewire\Attributes\Url;
use Webkul\Crm\Enums\ViewLayout;

trait HasViewLayouts
{
    #[Url(as: 'layout')]
    public ?string $viewLayout = null;

    abstract public function getViewLayouts(): array;

    public function mountHasViewLayouts(): void
    {
        $routeLayout = ViewLayout::tryFrom((string) str((string) request()->route()?->getName())->afterLast('.'));

        if ($routeLayout
            && in_array($routeLayout, $this->getViewLayouts(), true)) {
            $this->redirect(static::getResource()::getUrl('index', [...request()->query(), 'layout' => $routeLayout->value]));
        }
    }

    public function getViewLayout(): ViewLayout
    {
        $layout = ViewLayout::tryFrom((string) $this->viewLayout);

        return in_array($layout, $this->getViewLayouts(), true) ? $layout : $this->getViewLayouts()[0];
    }

    public function isViewLayout(ViewLayout $layout): bool
    {
        return $this->getViewLayout() === $layout;
    }

    public function switchViewLayout(string $layout): void
    {
        $target = ViewLayout::tryFrom($layout);

        $this->viewLayout = (in_array($target, $this->getViewLayouts(), true) ? $target : $this->getViewLayouts()[0])->value;

        $currentFilters = $this->tableFilters ?? [];

        $this->bootedInteractsWithTable();

        $filtersForm = $this->getTableFiltersForm();
        $filtersForm->fill();
        $filtersForm->fill(array_replace_recursive($filtersForm->getRawState(), $currentFilters));

        if ($this->getTable()->hasDeferredFilters()) {
            $this->tableFilters = $this->tableDeferredFilters;
        }

        if (blank($this->tableGrouping)
            && $this->getTable()->isDefaultGroupSelectable()) {
            $this->tableGrouping = $this->getTable()->getDefaultGroup()->getId().':'.$this->getTable()->getDefaultGroupDirection();

            $this->bootedInteractsWithTable();
        }

        if ($this->getTable()->isPaginated()) {
            $this->tableRecordsPerPage ??= $this->getDefaultTableRecordsPerPageSelectOption();
        }

        $this->resetPage();

        $this->flushCachedTableRecords();
    }
}
