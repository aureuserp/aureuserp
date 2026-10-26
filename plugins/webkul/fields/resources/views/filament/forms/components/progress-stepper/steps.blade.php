@use('Filament\Support\Icons\Heroicon')

@php
    $id = $getId();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
    $statePath = $getStatePath();
@endphp

<div
    {{
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->merge($getExtraAttributes(), escape: false)
            ->merge(['role' => 'list'])
            ->class([
                'fi-progress-stepper',
                'fi-inline' => $isInline(),
            ])
    }}
>
    @foreach ($options as $value => $label)
        @php
            $inputId = "{$id}-{$value}";
            $isStepDisabled = $isDisabled || $isOptionDisabled($value, $label);
            $isComplete = $currentIndex !== false && $loop->index < $currentIndex;
            $optionIcon = $getIcon($value);
        @endphp

        <label
            for="{{ $inputId }}"
            role="listitem"
            @class([
                'fi-progress-stepper-step',
                'fi-complete' => $isComplete,
                'fi-disabled' => $isStepDisabled,
            ])
        >
            <input
                @disabled($isStepDisabled)
                id="{{ $inputId }}"
                @if (! $isMultiple)
                    name="{{ $id }}"
                @endif
                type="{{ $isMultiple ? 'checkbox' : 'radio' }}"
                value="{{ $value }}"
                wire:loading.attr="disabled"
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                {{ $getExtraInputAttributeBag()->class(['sr-only']) }}
            />

            @if ($isComplete)
                <x-filament::icon :icon="Heroicon::Check" class="fi-progress-stepper-step-icon" />
            @elseif ($optionIcon)
                <x-filament::icon :icon="$optionIcon" class="fi-progress-stepper-step-icon" />
            @endif

            {{ $label }}
        </label>
    @endforeach
</div>
