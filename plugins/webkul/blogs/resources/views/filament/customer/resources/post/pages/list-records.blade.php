@if ($records->count())
    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
        @foreach ($records as $record)
            <div class="max-w-md overflow-hidden bg-white shadow-md rounded-xl md:max-w-2xl">
                <a href="{{ $record->category ? self::getResource()::getUrl('posts.view', ['category' => $record->category->slug, 'record' => $record->slug]) : self::getResource()::getUrl('index')}}">
                    <div class="md:shrink-0">
                        @if ($record->image_url)
                            <img class="object-cover w-full h-48 md:h-full md:w-48" src="{{$record->image_url}}" alt="Blog post featured image" style="aspect-ratio: 2 / 1" />
                        @else
                            <div class="object-cover w-full h-48 rounded-md bg-primary-500 md:h-full md:w-48" style="aspect-ratio: 2 / 1"></div>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="text-sm font-semibold tracking-wide uppercase text-primary-500">
                            {{ $record->category?->name }}
                        </div>

                        <div class="block mt-1 text-lg font-medium leading-tight text-black">
                            {{ $record->title }}
                        </div>

                        <p class="mt-2 text-gray-500">
                            {!! \Illuminate\Support\Str::limit($record->sub_title ?? $record->content, 150, $end='...') !!}
                        </p>

                        @if ($record->tags->count())
                            <div class="flex gap-4 mt-4">
                                @foreach ($record->tags as $tag)
                                    <x-filament::badge
                                        :color="$tag->color ? \Filament\Support\Colors\Color::hex($tag->color) : 'primary'"
                                    >
                                        {{ $tag->name }}
                                    </x-filament::badge>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center mt-4">
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
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <x-filament::pagination :paginator="$records" />
@else
    <div class="flex flex-col items-center justify-center py-12 text-center">
        <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path fill-rule="evenodd" d="M4.5 3.75A.75.75 0 0 1 5.25 3h13.5a.75.75 0 0 1 .75.75v16.5a.75.75 0 0 1-1.28.53L12 15.81l-6.22 4.97a.75.75 0 0 1-1.28-.53V3.75ZM6 4.5v14.69l5.47-4.38a.75.75 0 0 1 .92 0l5.47 4.38V4.5H6Z" clip-rule="evenodd"/>
        </svg>
        <p class="mt-4 text-lg font-semibold text-gray-700">No blog posts found</p>
        <p class="mt-2 text-gray-500">Check back later for new content.</p>
    </div>
@endif
