<?php

namespace Webkul\FullCalendar\Concerns;

use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;

trait InteractsWithHeaderActions
{
    protected array $cachedHeaderActions = [];

    public function bootedInteractsWithHeaderActions(): void
    {
        $this->cacheHeaderActions();
    }

    protected function cacheHeaderActions(): void
    {
        $actions = $this->headerActions();

        foreach ($actions as $action) {
            if ($action instanceof ActionGroup) {
                $action->livewire($this);

                if (! $action->getDropdownPlacement()) {
                    $action->dropdownPlacement('bottom-end');
                }

                $flatActions = $action->getFlatActions();

                $this->mergeCachedActions($flatActions);

                $this->cachedHeaderActions[] = $action;

                continue;
            }

            $this->cacheAction($action);

            $this->cachedHeaderActions[] = $action;
        }
    }

    public function getCachedHeaderActions(): array
    {
        if (! $this->getModel()) {
            return [];
        }

        return $this->cachedHeaderActions;
    }

    public function getCachedHeaderActionsComponent(): Actions
    {
        return Actions::make($this->getCachedHeaderActions())
            ->container(Schema::make($this));
    }

    protected function headerActions(): array
    {
        return [];
    }
}
