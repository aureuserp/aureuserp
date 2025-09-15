<x-filament-panels::page>    @push('styles')
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

        <meta property="og:url" content="{{ self::getResource()::getUrl('view', ['record' => $record->slug]) }}" />
    @endPush

    <?php
        $categories = $this->getRecords();

        $posts = $this->getPosts();
    ?>

    <div class="flex gap-4">
        <a href="{{ self::getResource()::getUrl('index') }}" class="font-bold text-gray-500 hover:bg-primary-6">
            All
        </a>

        @foreach ($categories as $category)
            <a
                href="{{ self::getResource()::getUrl('view', ['record' => $category->slug]) }}"
                class="hover:bg-primary-6 font-bold {{ $category->id === $this->record->id ? 'text-primary-500' : 'text-gray-500' }}"
            >
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    @include('blogs::filament.customer.resources.post.pages.list-records', ['records' => $posts])
</x-filament-panels::page>
