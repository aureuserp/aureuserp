<link
    rel="manifest"
    crossorigin="use-credentials"
    href="{{ $config ? route('point-of-sale.pwa.manifest.config', ['config' => $config]) : route('point-of-sale.pwa.manifest') }}"
>

<meta name="theme-color" content="#2563eb">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $config?->name ?? __('point-of-sale::filament/pos/pages/registers.title') }}">
