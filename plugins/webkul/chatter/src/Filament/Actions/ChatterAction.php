<?php

namespace Webkul\Chatter\Filament\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ChatterAction extends Action
{
    protected Collection|array|null $activityPlans = null;

    protected string $resourceClass = '';

    protected string $followerMailViewPath = '';

    protected string $messageMailViewPath = '';

    protected bool $isFollowerActionVisible = true;

    protected bool $isMessageActionVisible = true;

    protected bool $isLogActionVisible = true;

    protected bool $isActivityActionVisible = true;

    protected bool $isFileActionVisible = true;

    protected bool|Closure|null $hasModalCloseButton = false;

    public static function getDefaultName(): ?string
    {
        return 'chatter.action';
    }

    public function activityPlans(Collection|array|null $activityPlans): static
    {
        $this->activityPlans = $activityPlans;

        return $this;
    }

    public function resource(string $resourceClass): static
    {
        $this->validateResource($resourceClass);
        $this->resourceClass = $resourceClass;

        return $this;
    }

    public function followerMailView(string|Closure|null $followerMailViewPath): static
    {
        $this->followerMailViewPath = $followerMailViewPath;

        return $this;
    }

    public function messageMailView(string|Closure|null $messageMailViewPath): static
    {
        $this->messageMailViewPath = $messageMailViewPath;

        return $this;
    }

    public function followerActionVisible(bool|Closure $isVisible): static
    {
        $this->isFollowerActionVisible = $this->evaluate($isVisible);

        return $this;
    }

    public function messageActionVisible(bool|Closure $isVisible): static
    {
        $this->isMessageActionVisible = $this->evaluate($isVisible);

        return $this;
    }

    public function logActionVisible(bool|Closure $isVisible): static
    {
        $this->isLogActionVisible = $this->evaluate($isVisible);

        return $this;
    }

    public function activityActionVisible(bool|Closure $isVisible): static
    {
        $this->isActivityActionVisible = $this->evaluate($isVisible);

        return $this;
    }

    public function fileActionVisible(bool|Closure $isVisible): static
    {
        $this->isFileActionVisible = $this->evaluate($isVisible);

        return $this;
    }

    public function isFileActionVisible(): bool
    {
        return $this->isFileActionVisible;
    }

    public function isFollowerActionVisible(): bool
    {
        return $this->isFollowerActionVisible;
    }

    public function isMessageActionVisible(): bool
    {
        return $this->isMessageActionVisible;
    }

    public function isLogActionVisible(): bool
    {
        return $this->isLogActionVisible;
    }

    public function isActivityActionVisible(): bool
    {
        return $this->isActivityActionVisible;
    }

    public function getActivityPlans(): Collection
    {
        if ($this->activityPlans instanceof Collection) {
            return $this->activityPlans;
        }

        if (is_array($this->activityPlans)) {
            return collect($this->activityPlans);
        }

        return collect();
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }

    public function getFollowerMailViewPath(): string|Closure|null
    {
        return $this->followerMailViewPath;
    }

    public function getMessageMailViewPath(): string|Closure|null
    {
        return $this->messageMailViewPath;
    }

    /** @deprecated Use activityPlans() instead */
    public function setActivityPlans(mixed $activityPlans): static
    {
        return $this->activityPlans($activityPlans);
    }

    /** @deprecated Use resource() instead */
    public function setResource(string $resource): static
    {
        return $this->resource($resource);
    }

    /** @deprecated Use followerMailView() instead */
    public function setFollowerMailView(string|Closure|null $followerViewMail): static
    {
        return $this->followerMailView($followerViewMail);
    }

    /** @deprecated Use messageMailView() instead */
    public function setMessageMailView(string|Closure|null $messageViewMail): static
    {
        return $this->messageMailView($messageViewMail);
    }

    /** @deprecated Use followerActionVisible() instead */
    public function showFollowerAction(bool|Closure $showFollowerAction): static
    {
        return $this->followerActionVisible($showFollowerAction);
    }

    /** @deprecated Use messageActionVisible() instead */
    public function showMessageAction(bool|Closure $showMessageAction): static
    {
        return $this->messageActionVisible($showMessageAction);
    }

    /** @deprecated Use logActionVisible() instead */
    public function showLogAction(bool|Closure $showLogAction): static
    {
        return $this->logActionVisible($showLogAction);
    }

    /** @deprecated Use activityActionVisible() instead */
    public function showActivityAction(bool|Closure $showActivityAction): static
    {
        return $this->activityActionVisible($showActivityAction);
    }

    /** @deprecated Use fileActionVisible() instead */
    public function showFileAction(bool|Closure $showFileAction): static
    {
        return $this->fileActionVisible($showFileAction);
    }

    /** @deprecated Use isFileActionVisible() instead */
    public function getShowFileAction(): bool
    {
        return $this->isFileActionVisible();
    }

    /** @deprecated Use isFollowerActionVisible() instead */
    public function getShowFollowerAction(): bool
    {
        return $this->isFollowerActionVisible();
    }

    /** @deprecated Use isMessageActionVisible() instead */
    public function getShowMessageAction(): bool
    {
        return $this->isMessageActionVisible();
    }

    /** @deprecated Use isLogActionVisible() instead */
    public function getShowLogAction(): bool
    {
        return $this->isLogActionVisible();
    }

    /** @deprecated Use isActivityActionVisible() instead */
    public function getShowActivityAction(): bool
    {
        return $this->isActivityActionVisible();
    }

    /** @deprecated Use getResourceClass() instead */
    public function getResource(): string
    {
        return $this->getResourceClass();
    }

    /** @deprecated Use getFollowerMailViewPath() instead */
    public function getFollowerMailView(): string|Closure|null
    {
        return $this->getFollowerMailViewPath();
    }

    /** @deprecated Use getMessageMailViewPath() instead */
    public function getMessageMailView(): string|Closure|null
    {
        return $this->getMessageMailViewPath();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->configureModal();
    }

    protected function configureModal(): void
    {
        $this
            ->slideOver()
            ->hiddenLabel()
            ->icon(Heroicon::ChatBubbleLeftRight)
            ->modalIcon(Heroicon::ChatBubbleLeftRight)
            ->modalIconColor('primary')
            ->modalWidth(Width::TwoExtraLarge)
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->closeModalByEscaping()
            ->modalHeading(__('chatter::filament/resources/actions/chatter-action.title'))
            ->badge(fn (Model $record): int => $record->unRead()->count())
            ->modalContent(fn (Model $record): View => $this->renderModalContent($record));
    }

    protected function renderModalContent(Model $record): View
    {
        return tap(
            view('chatter::filament.widgets.chatter', [
                'record'                  => $this->getRecord(),
                'resourceClass'           => $this->getResourceClass(),
                'followerMailViewPath'    => $this->getFollowerMailViewPath(),
                'isFileActionVisible'     => $this->isFileActionVisible(),
                'isFollowerActionVisible' => $this->isFollowerActionVisible(),
                'chatterAction'           => $this,
            ]),
            fn () => $record->markAsRead()
        );
    }

    public function renderModal(): View
    {
        return view('chatter::filament.actions.chatter-action-modal', [
            'record'              => $this->getRecord(),
            'resourceClass'       => $this->getResourceClass(),
            'messageMailViewPath' => $this->getMessageMailViewPath(),
            'activityPlans'       => $this->getActivityPlans(),
            'chatterAction'       => $this,
        ]);
    }

    protected function validateResource(string $resourceClass): void
    {
        if (empty($resourceClass)) {
            throw new InvalidArgumentException('The resource parameter must be provided and cannot be empty.');
        }

        if (! class_exists($resourceClass)) {
            throw new InvalidArgumentException("The resource class [{$resourceClass}] does not exist.");
        }
    }
}
