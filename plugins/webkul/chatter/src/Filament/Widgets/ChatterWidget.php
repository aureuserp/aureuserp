<?php

namespace Webkul\Chatter\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ChatterWidget extends Widget
{
    protected string $view = 'chatter::filament.widgets.chatter';

    protected int|string|array $columnSpan = 'full';

    public $record = null;

    public mixed $activityPlans;

    public string $resource = '';

    public mixed $followerViewMail;

    public mixed $messageViewMail;

    protected static string $type = 'footer';

    public function mount($record = null, $followerViewMail = null, $messageViewMail = null, $resource = '', $activityPlans = [])
    {
        $this->record = $record;

        if ($activityPlans instanceof Collection) {
            $this->activityPlans = $activityPlans;
        } else {
            $this->activityPlans = collect($activityPlans);
        }

        $this->followerViewMail = $followerViewMail;

        $this->messageViewMail = $messageViewMail;

        $this->resource = $resource;
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
