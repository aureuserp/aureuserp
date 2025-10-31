<?php

namespace Webkul\Support\Concerns;

use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;

trait HasRepeatableEntryColumnManager
{
    public function applyRepeaterColumnManager(string $repeaterKey, array $columns): void
    {
        $repeater = $this->getRepeaterComponent($repeaterKey);
        if ($repeater) {
            $repeater->applyTableColumnManager($columns);
        }
    }

    public function resetRepeaterColumnManager(string $repeaterKey): void
    {
        $repeater = $this->getRepeaterComponent($repeaterKey);

        if ($repeater) {
            $repeater->resetTableColumnManager();
        }
    }

    protected function getRepeaterComponent(string $repeaterKey): ?RepeatableEntry
    {

        $infolist = $this->infolist->getFlatComponents();

        foreach ($infolist as $component) {
            if ($component instanceof RepeatableEntry && $component->getStatePath() === $repeaterKey) {
                return $component;
            }

            if (method_exists($component, 'getChildComponents')) {
                $found = $this->findRepeaterInComponents($component->getChildComponents(), $repeaterKey);

                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    protected function findRepeaterInComponents(array $components, string $repeaterKey): ?RepeatableEntry
    {
        foreach ($components as $component) {
            if ($component instanceof RepeatableEntry && $component->getStatePath() === $repeaterKey) {
                return $component;
            }

            if (method_exists($component, 'getChildComponents')) {
                $found = $this->findRepeaterInComponents($component->getChildComponents(), $repeaterKey);

                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }
}
