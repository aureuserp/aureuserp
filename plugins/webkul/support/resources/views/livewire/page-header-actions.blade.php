<div class="flex flex-wrap items-center gap-3">
    @foreach ($this->getPageHeaderActions() as $action)
        {{ $action }}
    @endforeach

    <x-filament-actions::modals />
</div>
