@php
    $id = $getId();
    $isInline = $isInline();
    $currentState = $getState();
    
    // Handle Enum values
    if ($currentState instanceof \BackedEnum) {
        $currentState = $currentState->value;
    }
@endphp

<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div
        {{
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'state-container',
                    'flex justify-end flex-wrap' => $isInline,
                ])
        }}
    >
        @foreach ($getOptions() as $value => $label)
            @php
                $inputId = "{$id}-{$value}";
                $isChecked = ((string) $currentState === (string) $value);
            @endphp

            <div class="state">
                <input
                    disabled
                    @if($isChecked) checked @endif
                    id="{{ $inputId }}"
                    name="{{ $id }}"
                    type="radio"
                    value="{{ $value }}"
                    class="peer pointer-events-none absolute opacity-0"
                />

                <label
                    for="{{ $inputId }}"
                    @class([
                        'stage-button',
                        'fi-btn',
                        'fi-btn-color-' . $getColor($value),
                    ])
                    style="pointer-events: none;"
                >
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>
</x-dynamic-component>

@push('styles')
    <style>
        .stage-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            padding-left: 30px;
            padding-right: 20px;
            padding-top: 8px;
            padding-bottom: 8px;
            border: 1px solid var(--gray-300);
            box-shadow: none;
            min-height: 38px;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: white;
            color: var(--gray-700);
            position: relative;
            transition-duration: 75ms;
        }
        
        .dark .stage-button {
            background-color: var(--gray-900);
            border: 1px solid var(--gray-700);
            color: var(--gray-300);
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
            border-right: 1px solid var(--gray-700);
            border-top: 1px solid var(--gray-700);
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
            background-color: var(--primary-600);
            border: 1px solid var(--primary-600);
        }
        
        input:checked + .stage-button:after {
            background-color: var(--primary-600);
            border-right: 1px solid var(--primary-600);
            border-top: 1px solid var(--primary-600);
        }
        
        .dark input:checked + .stage-button {
            background-color: var(--primary-500);
            border: 1px solid var(--primary-500);
        }
        
        .dark input:checked + .stage-button:after {
            background-color: var(--primary-500);
            border-right: 1px solid var(--primary-500);
            border-top: 1px solid var(--primary-500);
        }
    </style>
@endpush
