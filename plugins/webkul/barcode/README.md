# Barcode Plugin

Barcode is a mobile-first AureusERP plugin for inventory operation workflows. It supports:

- barcode dashboard at `/admin/barcode`
- dedicated barcode login at `/admin/barcode/login`
- operation type listing
- transfer listing
- operation counting flow
- web scanner using bundled `html5-qrcode`
- web, iOS, and Android app shells
- optional server-backed NativePHP mobile shell against the hosted barcode URL

## Prerequisites

- `inventories` plugin
- `manufacturing` plugin
- Filament admin panel
- `nativephp/mobile` only if you are packaging the mobile app

## Installation

### 1. Copy the plugin

Copy this directory into the host app:

```text
plugins/webkul/barcode
```

### 2. Refresh autoload and package discovery

```bash
composer dump-autoload
php artisan package:discover --ansi
```

### 3. Install the plugin

```bash
php artisan barcode:install --no-interaction
```

### 4. Rebuild Filament assets

```bash
php artisan filament:assets --no-interaction
```

### 5. Patch NativePHP mobile files when needed

```bash
php artisan barcode:patch-native
```

Force-copy plugin-owned stubs if you need a full reset:

```bash
php artisan barcode:patch-native --force
```

## Routes

Expected routes:

- `/barcode`
- `/admin/barcode/login`
- `/admin/barcode`
- `/admin/barcode/operations/{operationType}`
- `/admin/barcode/operations/{operationType}/transfers/{operation}`

## Scanner

This plugin does not use the paid NativePHP scanner.

It uses bundled `html5-qrcode`, served locally from:

- `plugins/webkul/barcode/resources/dist/html5-qrcode.min.js`

## Required host-app changes outside the plugin

This plugin is distributed by copy/paste. Because of that, a few app-level changes still need to exist outside the plugin.

### 1. Root route bootstrap in `routes/web.php`

Jump / NativePHP may open `/` first. Without a native-aware root route, the app falls back to `/admin/login` instead of `/admin/barcode/login`.

Keep this in the host app:

```php
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Webkul\Barcode\Support\NativeApp;

Route::get('/', function () {
    if (NativeApp::requestIsNative()) {
        if (Auth::check()) {
            return redirect()->route('barcode.dashboard');
        }

        return redirect()->route('barcode.login');
    }

    return redirect()->route('filament.admin.auth.login');
});

Route::redirect('/login', '/admin/login')
    ->name('login');
```

### 2. NativePHP start URL

In `.env` and `.env.example`:

```dotenv
NATIVEPHP_START_URL=https://your-domain.tld/admin/barcode?nativephp=1
```

Use a full hosted barcode URL if the mobile app must talk to the central web server. Use a relative path only if you intentionally want the embedded NativePHP runtime.

### 3. NativePHP config

In `config/nativephp.php`:

```php
use Webkul\Barcode\Support\NativeApp;

'start_url' => env('NATIVEPHP_START_URL', NativeApp::startUrl()),
'permissions' => NativeApp::iosPermissions(),
```

### 4. Provider registration

The host app must load:

- `Webkul\Barcode\BarcodeServiceProvider`

For this repository, that is done in:

- `bootstrap/providers.php`

### 5. NativePHP mobile patching

If `nativephp/mobile` is installed, run:

```bash
php artisan barcode:patch-native
```

This patches the NativePHP mobile templates/projects for:

- hosted remote start URL support
- iOS media-capture permission for `html5-qrcode`
- Android camera permission / WebView permission handling
- hosted native header / sidebar payload handling

Run it again after `native:install`, after regenerating `nativephp/`, or after updating `nativephp/mobile`.

`--force` replaces the supported Android/iOS files with the stubs shipped in:

- `plugins/webkul/barcode/stubs/nativephp`

### 6. Android camera permission verification

The plugin declares Android camera requirements in:

- `plugins/webkul/barcode/nativephp.json`

Verify the final Android build includes:

- `android.permission.CAMERA`
- `android.hardware.camera.any`

## Notes

- Production HTTPS forcing can stay in `app/Providers/AppServiceProvider.php`.
- The barcode plugin handles the Jump exception internally in `src/BarcodeServiceProvider.php` by forcing `http` when Jump is detected.
- `app/Providers/NativeServiceProvider.php` does not need a barcode entry for the current copy/paste distribution model.

## Post-install checklist

1. `composer dump-autoload`
2. `php artisan package:discover --ansi`
3. `php artisan barcode:install --no-interaction`
4. `php artisan barcode:patch-native` if you are building the mobile app
5. `php artisan filament:assets --no-interaction`
6. verify `/admin/barcode/login`
7. verify `/admin/barcode`
8. verify native app opens barcode login, not `/admin/login`
