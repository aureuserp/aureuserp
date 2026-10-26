@php
    $currentState = $getState();

    if ($currentState instanceof \BackedEnum) {
        $currentState = $currentState->value;
    }

    $options = $getOptions();

    $currentIndex = array_search((string) $currentState, array_map('strval', array_keys($options)), true);
@endphp

@if ($isRenderedBesideSubNavigation())
    <template x-teleport="{{ $getSubNavigationSlotSelector() }}" class="fi-progress-stepper-teleport">
        @include('fields::filament.infolists.components.progress-stepper.steps')
    </template>
@else
    <x-dynamic-component
        :component="$getEntryWrapperView()"
        :entry="$entry"
    >
        @include('fields::filament.infolists.components.progress-stepper.steps')
    </x-dynamic-component>
@endif
