<x-filament::section class="h-full">
    <div class="flex h-full flex-col gap-4">
        <div class="flex items-center gap-3">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-primary-500/10 text-primary-600 dark:text-primary-400">
                <x-filament::icon :icon="$card['icon']" class="size-6" />
            </div>

            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                {{ $card['title'] }}
            </h3>
        </div>

        <p class="flex-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $card['description'] }}
        </p>

        <div>
            <x-filament::button
                tag="a"
                href="{{ $card['url'] }}"
                target="_blank"
                size="lg"
            >
                {{ $card['button'] }}
            </x-filament::button>
        </div>
    </div>
</x-filament::section>
