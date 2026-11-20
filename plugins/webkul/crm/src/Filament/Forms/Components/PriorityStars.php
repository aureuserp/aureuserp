<?php

namespace Webkul\Crm\Filament\Forms\Components;

use Filament\Forms\Components\ToggleButtons;
use Webkul\Crm\Enums\OpportunityPriority;

class PriorityStars extends ToggleButtons
{
    protected string $view = 'crm::filament.forms.components.priority-stars';

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(OpportunityPriority::class);

        $this->default(OpportunityPriority::LOW);
    }
}
