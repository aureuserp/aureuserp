<x-filament-panels::page>
    @push('styles')
        <meta name="description" content="{{ trim($record->meta_description) != "" ? $record->meta_description : \Illuminate\Support\Str::limit(strip_tags($record->content), 120, '') }}"/>

        <meta name="keywords" content="{{ $record->meta_keywords }}"/>

        <meta name="twitter:card" content="summary_large_image" />

        <meta name="twitter:title" content="{{ $record->name }}" />

        <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($record->content))) !!}" />

        <meta name="twitter:image:alt" content="" />

        <meta name="twitter:image" content="{{ $record->image_url }}" />

        <meta property="og:type" content="og:product" />

        <meta property="og:title" content="{{ $record->name }}" />

        <meta property="og:image" content="{{ $record->image_url }}" />

        <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($record->content))) !!}" />

        <meta property="og:url" content="{{ self::getResource()::$parentResource::getUrl('posts.view', ['category' => $record->category->slug, 'record' => $record->slug]) }}" />
    @endPush

    @php
        $blogResource = self::getResource()::$parentResource;

        $subTitle = trim(strip_tags((string) $record->sub_title));
    @endphp

    <article class="flex w-full flex-col gap-5">
        <a
            href="{{ $blogResource::getUrl('index') }}"
            class="inline-flex w-fit items-center gap-1 text-xs font-medium text-gray-500 transition duration-75 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400"
        >
            <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::ArrowLeft" class="size-3.5" />

            {{ __('blogs::filament/customer/resources/post/pages/view-post.back') }}
        </a>

        <header class="flex flex-col gap-3">
            @if ($record->category)
                <a
                    href="{{ $blogResource::getUrl('view', ['record' => $record->category->slug]) }}"
                    class="w-fit text-xs font-semibold tracking-wide text-primary-600 uppercase hover:text-primary-500 dark:text-primary-400"
                >
                    {{ $record->category->name }}
                </a>
            @endif

            <h1 class="text-2xl font-bold tracking-tight text-gray-950 sm:text-3xl dark:text-white">
                {{ $record->title }}
            </h1>

            @if (filled($subTitle) && $subTitle !== trim(strip_tags((string) $record->title)))
                <p class="text-base text-gray-600 dark:text-gray-400">
                    {{ $subTitle }}
                </p>
            @endif

            <div class="flex items-center gap-2">
                <x-filament-panels::avatar.user
                    size="sm"
                    :user="$record->creator"
                />

                <div class="text-xs leading-4">
                    <p class="font-medium text-gray-950 dark:text-white">
                        {{ $record->creator->name }}
                    </p>

                    <p class="text-gray-500 dark:text-gray-400">
                        {{ $record->published_at->format('M j, Y').' · '.$record->reading_time }}
                    </p>
                </div>
            </div>
        </header>

        @if ($record->image_url)
            <img
                class="aspect-[3/1] w-full rounded-xl object-cover shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10"
                src="{{ $record->image_banner_url }}"
                alt="{{ $record->title }}"
                decoding="async"
                width="1200"
                height="400"
            />
        @endif

        <div class="text-sm leading-6 text-gray-700 dark:text-gray-300 [&_a]:font-medium [&_a]:text-primary-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-primary-400 [&_blockquote]:border-s-4 [&_blockquote]:border-gray-200 [&_blockquote]:ps-4 [&_blockquote]:italic dark:[&_blockquote]:border-white/10 [&_h1]:mt-8 [&_h1]:text-xl [&_h1]:font-bold [&_h1]:text-gray-950 dark:[&_h1]:text-white [&_h2]:mt-8 [&_h2]:text-lg [&_h2]:font-semibold [&_h2]:text-gray-950 dark:[&_h2]:text-white [&_h3]:mt-6 [&_h3]:text-base [&_h3]:font-semibold [&_h3]:text-gray-950 dark:[&_h3]:text-white [&_img]:my-6 [&_img]:rounded-xl [&_li]:mt-1 [&_ol]:mt-4 [&_ol]:list-decimal [&_ol]:ps-6 [&_p]:mt-4 [&_strong]:font-semibold [&_strong]:text-gray-950 dark:[&_strong]:text-white [&_ul]:mt-4 [&_ul]:list-disc [&_ul]:ps-6 [&>:first-child]:mt-0">
            {!! str($record->content)->sanitizeHtml() !!}
        </div>

        @if ($record->tags->count())
            <div class="flex flex-wrap gap-1.5 border-t border-gray-200 pt-4 dark:border-white/10">
                @foreach ($record->tags as $tag)
                    <x-filament::badge
                        tag="a"
                        size="sm"
                        :href="$blogResource::getUrl('index', ['tags' => $tag->getKey()])"
                        :color="$tag->color ? \Filament\Support\Colors\Color::hex($tag->color) : 'primary'"
                        class="transition duration-75 hover:opacity-75"
                    >
                        {{ $tag->name }}
                    </x-filament::badge>
                @endforeach
            </div>
        @endif
    </article>
</x-filament-panels::page>
