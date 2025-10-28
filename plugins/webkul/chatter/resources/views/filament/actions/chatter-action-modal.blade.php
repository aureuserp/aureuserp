@php
    use Filament\Actions\View\ActionsRenderHook;
    use Filament\Support\Facades\FilamentView;
    $modalConfig = [
        'alignment' => $chatterAction->getModalAlignment(),
        'autofocus' => $chatterAction->isModalAutofocused(),
        'closeButton' => $chatterAction->hasModalCloseButton(),
        'closeByClickingAway' => $chatterAction->isModalClosedByClickingAway(),
        'closeByEscaping' => $chatterAction->isModalClosedByEscaping(),
        'description' => $chatterAction->getModalDescription(),
        'extraAttributes' => $chatterAction->getExtraModalWindowAttributeBag(),
        'footerActions' => $chatterAction->getVisibleModalFooterActions(),
        'footerActionsAlignment' => $chatterAction->getModalFooterActionsAlignment(),
        'heading' => $chatterAction->getModalHeading(),
        'icon' => $chatterAction->getModalIcon(),
        'iconColor' => $chatterAction->getModalIconColor(),
        'id' => "fi-{$this->getId()}-action-{$chatterAction->getNestingIndex()}",
        'slideOver' => $chatterAction->isModalSlideOver(),
        'stickyFooter' => $chatterAction->isModalFooterSticky(),
        'stickyHeader' => $chatterAction->isModalHeaderSticky(),
        'width' => $chatterAction->getModalWidth(),
        'wireKey' => "{$this->getId()}.actions.{$chatterAction->getName()}.modal",
        'submitAction' => $chatterAction->hasFormWrapper() ? $chatterAction->getLivewireCallMountedActionName() : null,
    ];
@endphp

<x-filament::modal 
    :alignment="$modalConfig['alignment']" 
    :autofocus="$modalConfig['autofocus']"
    :close-button="$modalConfig['closeButton']" 
    :close-by-clicking-away="$modalConfig['closeByClickingAway']"
    :close-by-escaping="$modalConfig['closeByEscaping']" 
    :description="$modalConfig['description']"
    :extra-modal-window-attribute-bag="$modalConfig['extraAttributes']" 
    :footer-actions="$modalConfig['footerActions']"
    :footer-actions-alignment="$modalConfig['footerActionsAlignment']" 
    :heading="$modalConfig['heading']"
    :icon="$modalConfig['icon']"
    :icon-color="$modalConfig['iconColor']"
    :id="$modalConfig['id']"
    :slide-over="$modalConfig['slideOver']"
    :sticky-footer="$modalConfig['stickyFooter']"
    :sticky-header="$modalConfig['stickyHeader']"
    :width="$modalConfig['width']"
    :wire:key="$modalConfig['wireKey']"
    :wire:submit.prevent="$modalConfig['submitAction']"
    :x-on:modal-closed="'if ($event.detail.id === ' . \Illuminate\Support\Js::from($modalConfig['id']) . ') $wire.unmountAction(false)'"
>
    <x-slot name="header">
        <div class="chatter-panel-header flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-4 flex-1">
                @if ($modalConfig['icon'])
                    <div class="fi-modal-icon-ctn">
                        <div class="fi-modal-icon-bg fi-color fi-color-{{ $modalConfig['iconColor'] }}">
                            {{ \Filament\Support\generate_icon_html($modalConfig['icon'], size: \Filament\Support\Enums\IconSize::Large) }}
                        </div>
                    </div>
                @endif

                <div class="flex-1">
                    <h2 class="fi-modal-heading">{{ $modalConfig['heading'] }}</h2>
                    
                    @if ($modalConfig['description'])
                        <p class="fi-modal-description">{{ $modalConfig['description'] }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                @livewire('chatter-header-actions', [
                    'record' => $record,
                    'resourceClass' => $resourceClass,
                    'messageMailViewPath' => $messageMailViewPath,
                    'activityPlans' => $activityPlans,
                    'chatterAction' => $chatterAction,
                ])
            </div>
        </div>
    </x-slot>

    {{ FilamentView::renderHook(ActionsRenderHook::MODAL_CUSTOM_CONTENT_BEFORE, scopes: static::class, data: ['action' => $chatterAction]) }}
    
    {{ $chatterAction->getModalContent() }}
    
    {{ FilamentView::renderHook(ActionsRenderHook::MODAL_CUSTOM_CONTENT_AFTER, scopes: static::class, data: ['action' => $chatterAction]) }}

    @if ($this->mountedActionHasSchema(mountedAction: $chatterAction))
        {{ FilamentView::renderHook(ActionsRenderHook::MODAL_SCHEMA_BEFORE, scopes: static::class, data: ['action' => $chatterAction]) }}
    
        {{ $this->getMountedActionSchema(mountedAction: $chatterAction) }}
    
        {{ FilamentView::renderHook(ActionsRenderHook::MODAL_SCHEMA_AFTER, scopes: static::class, data: ['action' => $chatterAction]) }}
    @endif

    {{ FilamentView::renderHook(ActionsRenderHook::MODAL_CUSTOM_CONTENT_FOOTER_BEFORE, scopes: static::class, data: ['action' => $chatterAction]) }}
    
    {{ $chatterAction->getModalContentFooter() }}
    
    {{ FilamentView::renderHook(ActionsRenderHook::MODAL_CUSTOM_CONTENT_FOOTER_AFTER, scopes: static::class, data: ['action' => $chatterAction]) }}
</x-filament::modal>

<style>
    .fi-modal-header:has(> .chatter-panel-header) {
        padding-block: .85rem;
    }
    .fi-modal-content:has(> .chatter-panel-container) {
        padding: 0.5rem 1rem 1rem;
    }
    .fi-modal-content {
        overflow-y: auto;
    }
    .fi-modal.fi-modal-slide-over>.fi-modal-window-ctn>.fi-modal-window {
        overflow-y: hidden !important;
    }
</style>