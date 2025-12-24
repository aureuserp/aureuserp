<?php

namespace Webkul\Accounting\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Account\Models\Journal;

class JournalChartsWidget extends Widget
{
    protected string $view = 'accounting::filament.widgets.journal-charts-widget';

    protected int|string|array $columnSpan = 'full';

    public function getJournals()
    {
        return Journal::where('show_on_dashboard', true)
            ->orderBy('id', 'asc')
            ->get();
    }
}
