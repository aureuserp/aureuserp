<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi',
        'dark' => filament()->hasDarkMode() && filament()->hasDarkModeForced(),
    ])
>
    @php
        $nativeBridgeEnabled = \Webkul\Barcode\Support\NativeApp::usesNativeNavigation();
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? __('barcode::app.title') }}</title>

        <style>
            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            [x-cloak='inline-flex'] {
                display: inline-flex !important;
            }
        </style>

        @filamentStyles

        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontPreloadHtml() }}
        {{ filament()->getMonoFontPreloadHtml() }}
        {{ filament()->getSerifFontPreloadHtml() }}
        {{ filament()->getFontHtml() }}
        {{ filament()->getMonoFontHtml() }}
        {{ filament()->getSerifFontHtml() }}

        <style>
            :root {
                --font-family: '{!! filament()->getFontFamily() !!}';
                --mono-font-family: '{!! filament()->getMonoFontFamily() !!}';
                --serif-font-family: '{!! filament()->getSerifFontFamily() !!}';
                --default-theme-mode: {{ filament()->getDefaultThemeMode()->value }};
            }

            html.fi {
                --livewire-progress-bar-color: var(--primary-500);
            }
        </style>

        @livewireStyles
    </head>
    <body class="fi-body fi-panel-admin bg-gray-50 text-gray-950" x-data="{ sidebarOpen: false }">
        @if ($nativeBridgeEnabled)
            @include('barcode::components.sidebar.native')

            @if (filled(\Webkul\Barcode\Support\NativeApp::headerTitle()))
                @include('barcode::components.header.native', [
                    'title' => \Webkul\Barcode\Support\NativeApp::headerTitle(),
                    'subtitle' => \Webkul\Barcode\Support\NativeApp::headerSubtitle(),
                    'showBarcode' => \Webkul\Barcode\Support\NativeApp::shouldShowScanAction(),
                    'barcodeUrl' => \Webkul\Barcode\Support\NativeApp::scanActionUrl(),
                ])
            @endif

            <script id="barcode-native-ui" type="application/json">@json(\Native\Mobile\Edge\Edge::all())</script>
        @endif

        <div class="min-h-screen">
            @unless ($nativeBridgeEnabled)
                <div
                    class="fixed inset-0 z-39 bg-slate-900/45"
                    x-show="sidebarOpen"
                    x-transition.opacity
                    x-on:click="sidebarOpen = false"
                    x-cloak
                ></div>

                <aside
                    class="fixed top-0 left-0 z-40 w-[calc(100vw-40px)] max-w-80 will-change-transform"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    x-on:keydown.escape.window="sidebarOpen = false"
                    x-cloak
                >
                    @include('barcode::components.sidebar.web')
                </aside>
            @endunless

            <div class="min-w-0 flex-1">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
        @filamentScripts(withCore: true)
        <x-nativephp-remote::bridge-scripts />
    </body>
</html>
