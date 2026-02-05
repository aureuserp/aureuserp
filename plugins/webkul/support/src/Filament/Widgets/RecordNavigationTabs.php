<?php

namespace Webkul\Support\Filament\Widgets;

use Filament\Widgets\Widget;

class RecordNavigationTabs extends Widget
{
    protected string $view = 'support::filament.widgets.record-navigation-tabs';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public array $navigationItems = [];

    public function mount(array $navigationItems = []): void
    {
        $this->navigationItems = $navigationItems;
    }
}
