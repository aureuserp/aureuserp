@if ($records->count())
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($records as $record)
            @php
                $postUrl = $record->category
                    ? self::getResource()::getUrl('posts.view', ['category' => $record->category->slug, 'record' => $record->slug])
                    : self::getResource()::getUrl('index');

                $excerpt = trim(strip_tags((string) $record->sub_title));

                if ($excerpt === '' || $excerpt === trim(strip_tags((string) $record->title))) {
                    $excerpt = trim(html_entity_decode(strip_tags(str_replace(['<br>', '</p>'], ' ', (string) $record->content))));
                }
            @endphp

            <article class="group flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-900 dark:ring-white/10">
                <a href="{{ $postUrl }}" class="block aspect-[2/1] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                    @if ($record->image_url)
                        <img
                            class="size-full object-cover transition duration-300 group-hover:scale-105"
                            src="{{ $record->image_thumb_url }}"
                            alt="{{ $record->title }}"
                            loading="lazy"
                            decoding="async"
                            width="600"
                            height="300"
                        />
                    @else
                        <div class="flex size-full items-center justify-center bg-primary-50 text-primary-400 dark:bg-primary-950 dark:text-primary-500">
                            <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::OutlinedPhoto" class="size-8" />
                        </div>
                    @endif
                </a>

                <div class="flex flex-1 flex-col gap-2 p-4">
                    @if ($record->category)
                        <a
                            href="{{ $this->getCategoryFilterUrl($record->category) }}"
                            class="text-xs font-semibold tracking-wide text-primary-600 uppercase hover:text-primary-500 dark:text-primary-400"
                        >
                            {{ $record->category->name }}
                        </a>
                    @endif

                    <a href="{{ $postUrl }}" class="line-clamp-2 text-sm font-semibold leading-snug text-gray-950 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
                        {{ $record->title }}
                    </a>

                    @if (filled($excerpt))
                        <p class="line-clamp-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
                            {{ \Illuminate\Support\Str::limit($excerpt, 160) }}
                        </p>
                    @endif

                    @if ($record->tags->count())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($record->tags as $tag)
                                <x-filament::badge
                                    tag="a"
                                    size="sm"
                                    :href="$this->getTagFilterUrl($tag)"
                                    :color="$tag->color ? \Filament\Support\Colors\Color::hex($tag->color) : 'primary'"
                                    :icon="$this->isTagActive($tag) ? \Filament\Support\Icons\Heroicon::XMark : null"
                                    icon-position="after"
                                    class="transition duration-75 hover:opacity-75"
                                >
                                    {{ $tag->name }}
                                </x-filament::badge>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-auto flex items-center gap-2 border-t border-gray-100 pt-3 dark:border-white/10">
                        <x-filament-panels::avatar.user
                            size="sm"
                            :user="$record->creator"
                        />

                        <div class="min-w-0 text-xs leading-4">
                            <p class="truncate font-medium text-gray-950 dark:text-white">
                                {{ $record->creator->name }}
                            </p>

                            <p class="text-gray-500 dark:text-gray-400">
                                {{ $record->published_at->format('M j, Y').' · '.$record->reading_time }}
                            </p>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <x-filament::pagination :paginator="$records" />
@else
    <x-filament::empty-state
        :icon="\Filament\Support\Icons\Heroicon::OutlinedBookmark"
        :heading="__('blogs::filament/customer/resources/post/pages/list-records.empty.title')"
        :description="__('blogs::filament/customer/resources/post/pages/list-records.empty.description')"
    />
@endif
