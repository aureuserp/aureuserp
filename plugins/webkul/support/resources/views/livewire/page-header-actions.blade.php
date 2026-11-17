<div class="flex flex-wrap items-center gap-3">
    @foreach ($this->getPageHeaderActions() as $action)
        @if ($action->isVisible())
            {{ $action }}
        @endif
    @endforeach

    <x-filament-actions::modals />
</div>
