@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\View\ComponentAttributeBag;
    use function Filament\Support\generate_icon_html;
	use function Filament\Support\generate_loading_indicator_html;

    $key = $getKey();
    $statePath = $getStatePath();
	$state = $field->getState();
	$isDropdown = $isDropdown();
	$isDisabled = $isDisabled();
	$shouldCloseOnSelect = $shouldCloseOnSelect();
    $displayName = $getDisplayName();
    $placeholder = $getPlaceholder();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-load
        x-load-src="{{ FilamentAsset::getAlpineComponentSrc('icon-picker-component', 'guava/filament-icon-picker') }}"
        x-data="iconPickerComponent({
                key: @js($key),
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                displayName: @js($displayName),
                isDropdown: @js($isDropdown),
                shouldCloseOnSelect: @js($shouldCloseOnSelect),
                getSetUsing: async(state) => {
                    return await $wire.callSchemaComponentMethod(@js($key), 'getSetJs', { state })
                },
                getIconsUsing: async (set) => {
                    if (! $el._areIconsRequested) {
                        await new Promise((resolve) => $el.addEventListener('icon-picker-requested', resolve, { once: true }))
                    }

                    for (let attempt = 0; attempt < 5; attempt++) {
                        let icons = await $wire.callSchemaComponentMethod(@js($key), 'getIconsJs', { set })

                        if (icons !== null && icons !== undefined) {
                            return icons
                        }

                        await new Promise((resolve) => setTimeout(resolve, 200))
                    }

                    return []
                },
                getIconSvgUsing: async(id) => {
                    return await $wire.callSchemaComponentMethod(@js($key), 'getIconSvgJs', { id })
                },
                verifyStateUsing: async(state) => {
                    if (! state) {
                        return state
                    }

                    return await $wire.callSchemaComponentMethod(@js($key), 'verifyState', { state })
                }
            })"
        x-on:pointerdown.once="$el._areIconsRequested = true; $el.dispatchEvent(new CustomEvent('icon-picker-requested'))"
        x-on:focusin.once="$el._areIconsRequested = true; $el.dispatchEvent(new CustomEvent('icon-picker-requested'))"
        {{ $getExtraAttributeBag()
            ->class([
                'flex flex-col gap-4'
            ])
        }}
    >
        <x-filament::input.wrapper
            x-bind="dropdownTrigger"
            class="w-full relative"
            :disabled="$isDisabled"
            :inline-suffix="true"
            x-bind:class="{
                    '[&_.fi-input-wrp-prefix]:hidden': ! state,
                    '[&_.fi-input-wrp-suffix]:hidden': ! state,
                }"
            :valid="! $errors->has($statePath)"
        >
            <x-slot:prefix>
                <div x-data="{loading: false}">
                    <span x-cloak x-show="loading">{{generate_loading_indicator_html()}}</span>
                    <span x-show="! loading"
                          x-init="$watch('state', (newValue) => {
                                loading = true
                                setElementIcon($el, newValue, () => loading = false)
                             })"
                    >
                            @if($state)
                                {{generate_icon_html($state)}}
                            @endif
                        </span>
                </div>
            </x-slot:prefix>

            <x-filament::input type="hidden" x-model="state"/>
            <x-filament::input readonly x-model="displayName" value="{{$displayName}}" placeholder="{{$placeholder}}"/>

            @if(!$isDisabled)
                <x-slot:suffix>
                    {{
                        generate_icon_html(
                            'heroicon-s-x-mark',
                            attributes: (new ComponentAttributeBag())
                                ->merge([
                                    'x-on:click.prevent.stop' => 'updateState(null)'
                                ])
                                ->class([
                                    'opacity-50 text-black dark:text-white m-auto hover:cursor-pointer'
                                ])
                    )}}
                </x-slot:suffix>
            @endif

            @if($isDropdown && !$isDisabled)
                <x-guava-icon-picker::search :field="$field"
                                             :sets="$getAllowedSets()"
                                             :search-prompt="$getSearchPrompt()"
                                             :is-dropdown="$isDropdown"
                />
            @endif
        </x-filament::input.wrapper>

        @if(! $isDropdown && ! $isDisabled)
            <x-guava-icon-picker::search :field="$field"
                                         :sets="$getAllowedSets()"
                                         :search-prompt="$getSearchPrompt()"
                                         :is-dropdown="$isDropdown"
            />
        @endif
    </div>
</x-dynamic-component>
