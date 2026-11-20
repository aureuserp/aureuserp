<div class="flex flex-col gap-4">
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($palettes as $key => $palette)
            @php $isSelected = $selected === $key; @endphp

            <div @class([
                'flex flex-col gap-2.5 rounded-xl border p-3 transition',
                'border-primary-500 bg-primary-50 dark:border-primary-400 dark:bg-primary-400/10' => $isSelected,
                'border-gray-200 dark:border-white/10' => ! $isSelected,
            ])>
                <div class="flex items-center justify-between gap-1">
                    <span class="truncate text-xs font-semibold text-gray-900 dark:text-white">{{ $palette['name'] }}</span>

                    @if ($isSelected)
                        <x-filament::icon icon="heroicon-s-check-circle" class="h-4 w-4 shrink-0 text-primary-600 dark:text-primary-400" />
                    @endif
                </div>

                <div class="flex items-center gap-1">
                    @foreach ($palette['colors'] as $hex)
                        <span class="h-4 w-4 rounded-full ring-1 ring-gray-950/10 dark:ring-white/10" style="background-color: {{ $hex }}"></span>
                    @endforeach
                </div>

                <x-filament::button
                    wire:click="applyPalette('{{ $key }}')"
                    size="xs"
                    :color="$isSelected ? 'primary' : 'gray'"
                    :outlined="! $isSelected"
                    class="w-full justify-center"
                >
                    {{ __('support::filament/clusters/manage-branding.form.palettes.apply') }}
                </x-filament::button>
            </div>
        @endforeach
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('support::filament/clusters/manage-branding.form.palettes.custom-hint') }}</p>
</div>
