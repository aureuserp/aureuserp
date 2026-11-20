@php
    $state = $getState();
    $value = (int) ($state instanceof BackedEnum ? $state->value : $state);
    $labels = $getOptions();
    $isDisabled = $isDisabled();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :has-inline-label="$hasInlineLabel()"
>
    <x-slot name="label">
        {{ $getLabel() }}
    </x-slot>

    <div
        data-priority-stars
        x-data="{
            value: {{ Js::from($value) }},
            labels: {{ Js::from($labels) }},
            select(star) {
                this.value = this.value === star ? 0 : star

                $wire.$set({{ Js::from($getStatePath()) }}, this.value, false)
            },
        }"
        x-bind:title="labels[value]"
        title="{{ $labels[$value] ?? '' }}"
        class="flex items-center gap-0.5"
    >
        @foreach ($labels as $star => $starLabel)
            @continue ((int) $star < 1)

            <button
                type="button"
                @if ($isDisabled) disabled @else x-on:click="select({{ $star }})" @endif
                aria-label="{{ $starLabel }}"
                @class(['p-0.5 transition', 'hover:scale-110' => ! $isDisabled, 'cursor-default opacity-70' => $isDisabled])
            >
                <x-filament::icon
                    icon="heroicon-s-star"
                    class="h-6 w-6 text-warning-400"
                    x-show="value >= {{ $star }}"
                    @style(['display: none' => $star > $value])
                />

                <x-filament::icon
                    icon="heroicon-o-star"
                    class="h-6 w-6 text-gray-300 dark:text-gray-600"
                    x-show="value < {{ $star }}"
                    @style(['display: none' => $star <= $value])
                />
            </button>
        @endforeach
    </div>
</x-dynamic-component>
