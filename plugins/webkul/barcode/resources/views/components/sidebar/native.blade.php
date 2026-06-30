@php
    $items = \Webkul\Barcode\Support\Navigation::items();
@endphp

<native:side-nav>
    <native:side-nav-header
        title="{{ __('barcode::app.title') }}"
        subtitle="Navigation"
        icon="apps"
        :pinned="true"
    />

    @foreach ($items as $item)
        @continue($item['disabled'] || blank($item['href']))

        <native:side-nav-item
            id="{{ $item['id'] }}"
            label="{{ $item['label'] }}"
            icon="{{ $item['native_icon'] }}"
            url="{{ $item['href'] }}"
            :active="$item['active']"
        />
    @endforeach
</native:side-nav>
