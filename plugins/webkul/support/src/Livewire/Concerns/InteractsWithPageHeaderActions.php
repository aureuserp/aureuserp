<?php

namespace Webkul\Support\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Webkul\Support\Filament\Customization\PanelCustomizer;

trait InteractsWithPageHeaderActions
{
    protected array $pageHeaderActions = [];

    public function cacheInteractsWithPageHeaderActions(): void
    {
        $record = $this->getPageRecord();

        foreach (PanelCustomizer::headerActionsFor($this->page, $record) as $action) {
            if ($record) {
                $action->record($record);
            }

            if ($action instanceof ActionGroup) {
                $action->livewire($this);

                $this->mergeCachedActions($action->getFlatActions());
            } elseif ($action instanceof Action) {
                $this->cacheAction($action);
            }

            $this->pageHeaderActions[] = $action;
        }
    }

    public function getPageHeaderActions(): array
    {
        return $this->pageHeaderActions;
    }
}
