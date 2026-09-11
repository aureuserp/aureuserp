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

        <meta property="og:url" content="{{ self::getResource()::getUrl('view', ['record' => $record->slug]) }}" />
    @endPush

    <?php
        $categories = $this->getRecords();

        $posts = $this->getPosts();
    ?>

    @include('blogs::filament.customer.resources.category.partials.filters', ['categories' => $categories, 'activeCategory' => $record])

    @include('blogs::filament.customer.resources.post.pages.list-records', ['records' => $posts])
</x-filament-panels::page>
