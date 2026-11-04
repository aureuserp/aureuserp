<div
    role="tablist"
    aria-label="{{ __('crm::filament/view-layout-switcher.label') }}"
    class="crm-layout-switcher"
>
    @foreach ($layouts as $layout)
        <x-filament::icon-button
            :color="$activeLayout === $layout ? 'primary' : 'gray'"
            :icon="$layout->getIcon()"
            :label="$layout->getLabel()"
            :tooltip="$layout->getLabel()"
            role="tab"
            :aria-selected="$activeLayout === $layout ? 'true' : 'false'"
            wire:click="switchViewLayout('{{ $layout->value }}')"
        />
    @endforeach
</div>
