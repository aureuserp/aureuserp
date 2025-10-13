<?php

namespace Webkul\FullCalendar\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use InvalidArgumentException;

trait InteractsWithModalActions
{
    protected array $cachedModalActions = [];

    public function bootedInteractsWithModalActions(): void
    {
        $this->cacheModalActions();
    }

    protected function cacheModalActions(): void
    {
        foreach ($this->modalActions() as $action) {
            if ($action instanceof ActionGroup) {
                $action->livewire($this);

                $flatActions = $action->getFlatActions();

                $this->mergeCachedActions($flatActions);

                $this->cachedModalActions[] = $action;

                continue;
            }

            if (! $action instanceof Action) {
                throw new InvalidArgumentException('Header actions must be an instance of '.Action::class.', or '.ActionGroup::class.'.');
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

    public function modalActions(): array
    {
        return [];
    }
}
