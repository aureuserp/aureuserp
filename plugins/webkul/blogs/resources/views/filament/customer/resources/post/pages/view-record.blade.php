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

    @if ($record->image_url)
        <div class="md:shrink-0">
            <img class="object-cover w-full h-48 rounded-md md:h-full md:w-48" src="{{$record->image_url}}" alt="Blog post featured image" style="aspect-ratio: 3 / 1"/>
        </div>
    @endif

    <p>
        {!! $record->content !!}
    </p>

    @if ($record->tags->count())
        <div class="flex gap-4">
            @foreach ($record->tags as $tag)
                <x-filament::badge
                    :color="$tag->color ? \Filament\Support\Colors\Color::hex($tag->color) : 'primary'"
                >
                    {{ $tag->name }}
                </x-filament::badge>
            @endforeach
        </div>
    @endif

    <div class="flex items-center">
        <x-filament-panels::avatar.user
            class="mr-4"
            :user="$record->creator"
        />

        <div>
            <p class="text-sm font-medium text-gray-900">
                {{ $record->creator->name }}
            </p>

            <p class="text-sm text-gray-500">
                {{ $record->published_at->format('F j, Y').' · '.$record->reading_time }}
            </p>
        </div>
    </div>
</x-filament-panels::page>
