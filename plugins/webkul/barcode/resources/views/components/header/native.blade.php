@props([
    'title',
    'subtitle' => null,
    'showBarcode' => false,
    'barcodeUrl' => null,
])

@if (filled($subtitle))
    <native:top-bar
        title="{{ $title }}"
        subtitle="{{ $subtitle }}"
        show-navigation-icon="true"
    >
        <native:top-bar-action
            id="home"
            label="Home"
            icon="home"
            url="{{ \Webkul\Barcode\Support\NativeApp::navigationUrl('barcode.dashboard') }}"
        />
        @if ($showBarcode && filled($barcodeUrl))
            <native:top-bar-action
                id="scan"
                label="Scan"
                icon="qr-code"
                url="{{ $barcodeUrl }}"
            />
        @endif
    </native:top-bar>
@else
    <native:top-bar
        title="{{ $title }}"
        show-navigation-icon="true"
    >
        <native:top-bar-action
            id="home"
            label="Home"
            icon="home"
            url="{{ \Webkul\Barcode\Support\NativeApp::navigationUrl('barcode.dashboard') }}"
        />
        @if ($showBarcode && filled($barcodeUrl))
            <native:top-bar-action
                id="scan"
                label="Scan"
                icon="qr-code"
                url="{{ $barcodeUrl }}"
            />
        @endif
    </native:top-bar>
@endif
