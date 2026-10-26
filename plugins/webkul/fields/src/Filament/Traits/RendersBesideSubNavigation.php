<?php

namespace Webkul\Field\Filament\Traits;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;

trait RendersBesideSubNavigation
{
    public static function getSubNavigationSlotHtml(): string
    {
        return '<div id="fi-page-progress-stepper" class="fi-progress-stepper-slot" wire:ignore></div>';
    }

    public function getSubNavigationSlotSelector(): string
    {
        return '#fi-page-progress-stepper';
    }

    public function isRenderedBesideSubNavigation(): bool
    {
        $livewire = $this->getLivewire();

        if (! $livewire instanceof Page) {
            return false;
        }

        if (! in_array($this->getRootContainer()->getKey(), ['form', 'infolist'], true)) {
            return false;
        }

        if (method_exists($livewire, 'getRecordNavigationTabs')) {
            return filled($livewire->getRecordNavigationTabs());
        }

        return $livewire::getSubNavigationPosition() === SubNavigationPosition::Top
            && filled($livewire->getCachedSubNavigation());
    }
}
