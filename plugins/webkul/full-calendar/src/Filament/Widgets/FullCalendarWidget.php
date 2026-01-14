<?php

namespace Webkul\FullCalendar\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Webkul\FullCalendar\Concerns\CanBeConfigured;
use Webkul\FullCalendar\Concerns\InteractsWithEvents;
use Webkul\FullCalendar\Concerns\InteractsWithHeaderActions;
use Webkul\FullCalendar\Concerns\InteractsWithModalActions;
use Webkul\FullCalendar\Concerns\InteractsWithRawJS;
use Webkul\FullCalendar\Concerns\InteractsWithRecord;
use Webkul\FullCalendar\Contracts\HasConfigurations;
use Webkul\FullCalendar\Contracts\HasEvents;
use Webkul\FullCalendar\Contracts\HasHeaderActions;
use Webkul\FullCalendar\Contracts\HasModalActions;
use Webkul\FullCalendar\Contracts\HasRawJs;
use Webkul\FullCalendar\Contracts\HasRecords;

class FullCalendarWidget extends Widget implements HasActions, HasConfigurations, HasEvents, HasForms, HasHeaderActions, HasModalActions, HasRawJs, HasRecords
{
    use CanBeConfigured;
    use InteractsWithActions;
    use InteractsWithEvents;
    use InteractsWithForms;
    use InteractsWithHeaderActions;
    use InteractsWithModalActions;
    use InteractsWithRawJS;
    use InteractsWithRecord;

    protected string $view = 'full-calendar::filament.widgets.full-calendar';

    protected int|string|array $columnSpan = 'full';

    protected function headerActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make();
    }

    public function fetchEvents(array $info): array
    {
        return [];
    }

    public function getFormSchema(): array
    {
        return [];
    }
}
