@php
    $activeTags = $this->getActiveTags();

    $filterClasses = 'rounded-full px-3 py-1 text-xs font-medium transition duration-75';

    $activeFilterClasses = 'bg-primary-600 text-white shadow-sm dark:bg-primary-500';

    $inactiveFilterClasses = 'bg-white text-gray-700 ring-1 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-white/10';
@endphp

<div class="flex flex-col gap-3">
    <div class="flex flex-wrap gap-2">
        <a
            href="{{ $this->getCategoryFilterUrl(null) }}"
            @class([$filterClasses, $activeCategory ? $inactiveFilterClasses : $activeFilterClasses])
        >
            {{ __('blogs::filament/customer/resources/post/pages/list-records.filters.all') }}
        </a>

        @foreach ($categories as $category)
            <a
                href="{{ $this->getCategoryFilterUrl($category) }}"
                @class([$filterClasses, $activeCategory?->id === $category->id ? $activeFilterClasses : $inactiveFilterClasses])
            >
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    @if ($activeTags->count())
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ __('blogs::filament/customer/resources/post/pages/list-records.filters.tags') }}
            </span>

            @foreach ($activeTags as $activeTag)
                <x-filament::badge
                    tag="a"
                    size="sm"
                    :href="$this->getTagFilterUrl($activeTag)"
                    :color="$activeTag->color ? \Filament\Support\Colors\Color::hex($activeTag->color) : 'primary'"
                    :icon="\Filament\Support\Icons\Heroicon::XMark"
                    icon-position="after"
                    class="transition duration-75 hover:opacity-75"
                >
                    {{ $activeTag->name }}
                </x-filament::badge>
            @endforeach

            @if ($activeTags->count() > 1)
                <a
                    href="{{ $this->getClearTagsUrl() }}"
                    class="text-xs text-gray-500 underline transition duration-75 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    {{ __('blogs::filament/customer/resources/post/pages/list-records.filters.clear-tags') }}
                </a>
            @endif
        </div>
    @endif
</div>
