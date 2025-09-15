@php
    $gridDirection = $getGridDirection() ?? 'column';
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();
    $isMultiple = $isMultiple();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
>
    <x-slot
        name="label"
        @class([
            'sm:pt-1.5' => $hasInlineLabel,
        ])
    >
        {{ $getLabel() }}
    </x-slot>

    <div
        {{
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'state-container',
                    'grid' => ! $isInline && $gridDirection === 'row',
                    'flex justify-end flex-wrap' => $isInline,
                ])
        }}
    >
        {{ $getChildComponentContainer() }}

        @foreach ($getOptions() as $value => $label)
            @php
                $inputId = "{$id}-{$value}";
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
            @endphp

            <div
                @class([
                    'state' => true,
                    'border-primary-500',
                    'break-inside-avoid pt-3' => (! $isInline) && ($gridDirection === 'column'),
                ])
            >
                <input
                    @disabled($shouldOptionBeDisabled)
                    id="{{ $inputId }}"
                    @if (! $isMultiple)
                        name="{{ $id }}"
                    @endif
                    type="{{ $isMultiple ? 'checkbox' : 'radio' }}"
                    value="{{ $value }}"
                    wire:loading.attr="disabled"
                    {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                    {{ $getExtraInputAttributeBag()->class(['peer pointer-events-none absolute opacity-0']) }}
                />

                <x-filament::button
                    class="stage-button"
                    :color="$getColor($value)"
                    :for="$inputId"
                    :icon="$getIcon($value)"
                    tag="label"
                >
                    {{ $label }}
                </x-filament::button>
            </div>
        @endforeach
    </div>
</x-dynamic-component>

@push('styles')
    <style>
        .stage-button {
            border-radius: 0;
            padding-left: 30px;
            padding-right: 20px;
            border: 1px solid var(--gray-300);
            box-shadow: none;
            min-height: 38px;
        }
        .dark .stage-button {
            border: 1px solid var(--gray-700);
        }
        .stage-button:after {
            content: "";
            position: absolute;
            top: 50%;
            right: -14px;
            width: 26px;
            height: 26px;
            z-index: 1;
            transform: translateY(-50%) rotate(45deg);
            background-color: #ffffff;
            border-right: 1px solid var(--gray-300);
            border-top: 1px solid var(--gray-300);
            transition-duration: 75ms;
        }
        .dark .stage-button:after {
            background-color: var(--gray-900);
            border-right: 1px solid hsla(0, 0%, 100%, .2);
            border-top: 1px solid hsla(0, 0%, 100%, .2);
        }

        .dark .stage-button:hover:after {
            background-color: var(--gray-800);
        }
        .state-container .state:last-child .stage-button {
            border-radius: 0 8px 8px 0;
        }
        .state-container .state:first-child .stage-button {
            border-radius: 8px 0 0 8px;
        }
        .state-container .state:last-child .stage-button:after {
            content: none;
        }
        input:checked + .stage-button {
            color: #fff;
            border: 1px solid var(--color-500);
        }
        input:checked + .stage-button:after {
            background-color: var(--color-600);
            border-right: 1px solid var(--color-500);
            border-top: 1px solid var(--color-500);
        }
        .dark input:checked + .stage-button:after {
            background-color: var(--color-600);
        }
        input:checked + .stage-button:hover:after {
            background-color: var(--color-500);
            transition-duration: 75ms;
        }
        .dark input:checked + .stage-button:hover:after {
            background-color: var(--color-500);
        }
    </style>
@endpush
