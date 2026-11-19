<div
    role="tablist"
    aria-label="{{ __('projects::filament/kanban.switcher.label') }}"
    class="projects-layout-switcher"
>
    @foreach ($layouts as $layout)
        <x-filament::icon-button
            :color="$activeLayout === $layout ? 'primary' : 'gray'"
            :icon="$layout->getIcon()"
            :label="$layout->getLabel()"
            :tooltip="$layout->getLabel()"
            role="tab"
            :aria-selected="$activeLayout === $layout ? 'true' : 'false'"
            wire:click="switchTaskViewLayout('{{ $layout->value }}')"
        />
    @endforeach
</div>
