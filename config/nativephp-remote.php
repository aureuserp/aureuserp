<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Remote Shell Cookie
    |--------------------------------------------------------------------------
    |
    | Cookie name used to persist native shell detection across requests.
    | Set automatically when the app is opened with ?nativephp=1.
    |
    */
    'cookie_name' => env('NATIVEPHP_REMOTE_COOKIE', 'nativephp_remote_shell'),

    /*
    |--------------------------------------------------------------------------
    | Cookie Lifetime (minutes)
    |--------------------------------------------------------------------------
    */
    'cookie_lifetime' => 60 * 24 * 30, // 30 days

    /*
    |--------------------------------------------------------------------------
    | Android Permissions
    |--------------------------------------------------------------------------
    |
    | Permissions to add to AndroidManifest.xml when patching.
    |
    */
    'android_permissions' => [
        'android.permission.CAMERA',
        'android.permission.VIBRATE',
    ],

    /*
    |--------------------------------------------------------------------------
    | iOS Permissions
    |--------------------------------------------------------------------------
    |
    | Info.plist permission descriptions for iOS.
    |
    */
    'ios_permissions' => [
        'NSCameraUsageDescription' => 'This app uses your camera to scan barcodes and QR codes.',
    ],
];
