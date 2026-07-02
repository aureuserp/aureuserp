@php
    use Filament\Support\View\Components\ToggleComponent;
    use Illuminate\Support\Arr;

    $onClasses = Arr::toCssClasses(['fi-toggle-on', ...\Filament\Support\get_component_color_classes(ToggleComponent::class, 'primary')]);
    $offClasses = Arr::toCssClasses(['fi-toggle-off', ...\Filament\Support\get_component_color_classes(ToggleComponent::class, 'gray')]);
@endphp

<button
    type="button"
    role="switch"
    x-bind:aria-checked="({{ $on }})?.toString()"
    x-on:click="{{ $click }}"
    x-bind:class="({{ $on }}) ? @js($onClasses) : @js($offClasses)"
    class="fi-toggle {{ $sizeClass ?? '' }}"
>
    <div>
        <div aria-hidden="true"></div>
        <div aria-hidden="true"></div>
    </div>
</button>
