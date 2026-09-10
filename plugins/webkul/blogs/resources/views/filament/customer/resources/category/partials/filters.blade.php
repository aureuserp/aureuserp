@php
    $activeTags = $this->getActiveTags();
@endphp

<div class="flex flex-col gap-3">
    <div class="flex flex-wrap gap-4">
        <a
            href="{{ $this->getCategoryFilterUrl(null) }}"
            class="hover:bg-primary-6 font-bold {{ $activeCategory ? 'text-gray-500' : 'text-primary-500' }}"
        >
            {{ __('blogs::filament/customer/resources/post/pages/list-records.filters.all') }}
        </a>

        @foreach ($categories as $category)
            <a
                href="{{ $this->getCategoryFilterUrl($category) }}"
                class="hover:bg-primary-6 font-bold {{ $activeCategory?->id === $category->id ? 'text-primary-500' : 'text-gray-500' }}"
            >
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    @if ($activeTags->count())
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-500">
                {{ __('blogs::filament/customer/resources/post/pages/list-records.filters.tags') }}
            </span>

            @foreach ($activeTags as $activeTag)
                <x-filament::badge
                    tag="a"
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
                    class="text-sm text-gray-500 underline transition duration-75 hover:text-gray-700"
                >
                    {{ __('blogs::filament/customer/resources/post/pages/list-records.filters.clear-tags') }}
                </a>
            @endif
        </div>
    @endif
</div>
