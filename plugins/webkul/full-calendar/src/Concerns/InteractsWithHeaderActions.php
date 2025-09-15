<?php

namespace Webkul\FullCalendar\Concerns;

use Filament\Actions\ActionGroup;

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

    protected function headerActions(): array
    {
        return [];
    }
}
