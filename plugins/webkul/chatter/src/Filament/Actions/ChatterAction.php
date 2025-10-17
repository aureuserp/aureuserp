<?php

namespace Webkul\Chatter\Filament\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Webkul\Chatter\Filament\Actions\Chatter\ActivityAction;
use Webkul\Chatter\Filament\Actions\Chatter\LogAction;
use Webkul\Chatter\Filament\Actions\Chatter\MessageAction;

class ChatterAction extends Action
{
    protected mixed $activityPlans;

    protected string $resource = '';

    protected string $followerViewMail = '';

    protected string $messageViewMail = '';

    protected array|Closure $headerActions = [];

    protected bool|Closure|null $hasModalCloseButton = false;

    public static function getDefaultName(): ?string
    {
        return 'chatter.action';
    }

    public function setActivityPlans(mixed $activityPlans): static
    {
        $this->activityPlans = $activityPlans;

        return $this;
    }

    public function setResource(string $resource): static
    {
        if (empty($resource)) {
            throw new InvalidArgumentException('The resource parameter must be provided and cannot be empty.');
        }

        if (! class_exists($resource)) {
            throw new InvalidArgumentException("The resource class [{$resource}] does not exist.");
        }

        $this->resource = $resource;

        return $this;
    }

    public function setFollowerMailView(string|Closure|null $followerViewMail): static
    {
        $this->followerViewMail = $followerViewMail;

        return $this;
    }

    public function setMessageMailView(string|Closure|null $messageViewMail): static
    {
        $this->messageViewMail = $messageViewMail;

        return $this;
    }

    public function headerActions(array|Closure $actions): static
    {
        $this->headerActions = $actions;

        return $this;
    }

    public function getActivityPlans(): mixed
    {
        return $this->activityPlans ?? collect();
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getFollowerMailView(): string|Closure|null
    {
        return $this->followerViewMail;
    }

    public function getMessageMailView(): string|Closure|null
    {
        return $this->messageViewMail;
    }

    public function getHeaderActions(): array
    {
        $actions = $this->evaluate($this->headerActions);

        return ! is_array($actions) ? [] : $actions;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->hiddenLabel()
            ->icon(Heroicon::ChatBubbleLeftRight)
            ->modalIcon(Heroicon::ChatBubbleLeftRight)
            ->slideOver()
            ->modalIconColor('warning')
            ->closeModalByEscaping()
            ->modalContent(fn (Model $record): View => tap(view('chatter::filament.widgets.chatter', [
                'record'           => $record,
                'activityPlans'    => $this->getActivityPlans(),
                'resource'         => $this->getResource(),
                'followerViewMail' => $this->getFollowerMailView(),
                'messageViewMail'  => $this->getMessageMailView(),
            ]), fn () => $record->markAsRead()))
            ->modalHeading(__('chatter::filament/resources/actions/chatter-action.title'))
            ->badge(fn (Model $record): int => $record->unRead()->count())
            ->modalWidth(Width::TwoExtraLarge)
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->registerModalActions([
                MessageAction::make('message')
                    ->visible(true)
                    ->setMessageMailView($this->getMessageMailView())
                    ->setResource($this->getResource()),

                LogAction::make('log')
                    ->visible(true),

                ActivityAction::make('activity')
                    ->visible(true)
                    ->setActivityPlans($this->getActivityPlans()),
            ])
            ->headerActions([
                MessageAction::make('message'),
                LogAction::make('log'),
                ActivityAction::make('activity'),
            ]);
    }

    public function renderModal(): View
    {
        return view('chatter::filament.actions.chatter-action-modal', [
            'action' => $this,
        ]);
    }
}
