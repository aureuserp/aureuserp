<?php

namespace Webkul\Field\Filament\Forms\Components;

use Filament\Forms\Components\ToggleButtons;
use Webkul\Field\Filament\Traits\RendersBesideSubNavigation;

class ProgressStepper extends ToggleButtons
{
    use RendersBesideSubNavigation;

    protected string $view = 'fields::filament.forms.components.progress-stepper.index';
}
