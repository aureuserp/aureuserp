<?php

namespace Webkul\Chatter\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Webkul\Chatter\Filament\Actions\Chatter\ActivityAction;
use Webkul\Chatter\Filament\Actions\Chatter\LogAction;
use Webkul\Chatter\Filament\Actions\Chatter\MessageAction;
use Webkul\Chatter\Filament\Actions\ChatterAction;

class ChatterHeaderActions extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Model $record;

    public string $resourceClass = '';

    public string $messageMailViewPath = '';

    public Collection $activityPlans;

    private ?ChatterAction $chatterAction = null;

    public function mount(
        Model $record,
        string $resourceClass,
        string $messageMailViewPath,
        mixed $activityPlans = null,
        ChatterAction $chatterAction
    ): void {
        $this->record = $record;
        $this->resourceClass = $resourceClass;
        $this->messageMailViewPath = $messageMailViewPath;
        $this->activityPlans = $this->normalizeActivityPlans($activityPlans);
        $this->chatterAction = $chatterAction;
    }

    public function messageAction(): MessageAction
    {
        return MessageAction::make('message')
            ->visible($this->isActionVisible('message'))
            ->setMessageMailView($this->messageMailViewPath)
            ->setResource($this->resourceClass)
            ->record($this->record)
            ->slideOver(false);
    }

    public function logAction(): LogAction
    {
        return LogAction::make('log')
            ->visible($this->isActionVisible('log'))
            ->record($this->record)
            ->slideOver(false);
    }

    public function activityAction(): ActivityAction
    {
        return ActivityAction::make('activity')
            ->visible($this->isActionVisible('activity'))
            ->setActivityPlans($this->activityPlans)
            ->record($this->record)
            ->slideOver(false);
    }

    protected function isActionVisible(string $actionType): bool
    {
        $chatterAction = $this->getChatterActionInstance();

        return match ($actionType) {
            'message'  => $chatterAction->isMessageActionVisible(),
            'log'      => $chatterAction->isLogActionVisible(),
            'activity' => $chatterAction->isActivityActionVisible(),
            default    => false,
        };
    }

    protected function getChatterActionInstance(): ChatterAction
    {
        if ($this->chatterAction === null) {
            $this->chatterAction = ChatterAction::make('chatter');
        }

        return $this->chatterAction;
    }

    protected function normalizeActivityPlans(mixed $activityPlans): Collection
    {
        if ($activityPlans instanceof Collection) {
            return $activityPlans;
        }

        if (is_array($activityPlans)) {
            return collect($activityPlans);
        }

        return collect();
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                @foreach (['messageAction', 'logAction', 'activityAction'] as $action)
                    @if ($this->{$action}->isVisible())
                        {{ $this->{$action} }}
                    @endif
                @endforeach

                <x-filament-actions::modals />
            </div>
        BLADE;
    }
}
