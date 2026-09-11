<x-filament-panels::page>
    <?php
        $categories = $this->getRecords();

        $posts = $this->getPosts();
    ?>

    @include('blogs::filament.customer.resources.category.partials.filters', ['categories' => $categories, 'activeCategory' => null])

    @include('blogs::filament.customer.resources.post.pages.list-records', ['records' => $posts])
</x-filament-panels::page>
