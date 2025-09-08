<?php

namespace Webkul\FullCalendar\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

trait InteractsWithModalActions
{
    protected array $cachedModalActions = [];

    public function bootedInteractsWithModalActions(): void
    {
        $this->cacheModalActions();
    }

    protected function cacheModalActions(): void
    {
        $actions = $this->modalActions();

        foreach ($actions as $action) {
            if ($action instanceof ActionGroup) {
                $action->livewire($this);

                $flatActions = $action->getFlatActions();

                $this->mergeCachedActions($flatActions);

                $this->cachedModalActions[] = $action;

                continue;
            }

            $this->cacheAction($action);

            $this->cachedModalActions[] = $action;
        }
    }

    public function getCachedModalActions(): array
    {
        if (! $this->getModel()) {
            return [];
        }

        return $this->cachedModalActions;
    }

    protected function modalActions(): array
    {
        return [];
    }
}
