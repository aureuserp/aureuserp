@use('Filament\Support\Icons\Heroicon')

<ol
    {{
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-progress-stepper',
                'fi-inline' => $isInline(),
            ])
    }}
>
    @foreach ($options as $value => $label)
        @php
            $isComplete = $currentIndex !== false && $loop->index < $currentIndex;
            $isCurrent = $loop->index === $currentIndex;
        @endphp

        <li
            @class([
                'fi-progress-stepper-step',
                'fi-complete' => $isComplete,
                'fi-current' => $isCurrent,
            ])
            @if ($isCurrent) aria-current="step" @endif
        >
            @if ($isComplete)
                <x-filament::icon :icon="Heroicon::Check" class="fi-progress-stepper-step-icon" />
            @endif

            {{ $label }}
        </li>
    @endforeach
</ol>
