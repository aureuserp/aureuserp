<?php

namespace Webkul\Chatter\Filament\Widgets;

use Filament\Widgets\Widget;

class ChatterWidget extends Widget
{
    protected string $view = 'chatter::filament.widgets.chatter';

    protected int|string|array $columnSpan = 'full';

    public $record = null;

    protected static string $type = 'footer';

    public function mount($record = null)
    {
        $this->record = $record;
    }

    public static function canView(): bool
    {
        return true;
    }

    public function getRecord()
    {
        return $this->record;
    }
}
