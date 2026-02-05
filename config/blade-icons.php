<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Icons Sets
    |--------------------------------------------------------------------------
    |
    | With this config option you can define a couple of
    | default icon sets. Provide a key name for your icon
    | set and a combination from the options below.
    |
    */

    'sets' => [

        'default' => [

            /*
            |-----------------------------------------------------------------
            | Icons Path
            |-----------------------------------------------------------------
            |
            | Provide the relative path from your app root to your SVG icons
            | directory. Icons are loaded recursively so there's no need to
            | list every sub-directory.
            |
            | Relative to the disk root when the disk option is set.
            |
            */

            'path' => 'resources/svg',

            /*
            |-----------------------------------------------------------------
            | Filesystem Disk
            |-----------------------------------------------------------------
            |
            | Optionally, provide a specific filesystem disk to read
            | icons from. When defining a disk, the "path" option
            | starts relatively from the disk root.
            |
            */

            'disk' => '',

            /*
            |-----------------------------------------------------------------
            | Default Prefix
            |-----------------------------------------------------------------
            |
            | This config option allows you to define a default prefix for
            | your icons. The dash separator will be applied automatically
            | to every icon name. It's required and needs to be unique.
            |
            */

            'prefix' => 'icon',

            /*
            |-----------------------------------------------------------------
            | Fallback Icon
            |-----------------------------------------------------------------
            |
            | This config option allows you to define a fallback
            | icon when an icon in this set cannot be found.
            |
            */

            'fallback' => '',

            /*
            |-----------------------------------------------------------------
            | Default Set Classes
            |-----------------------------------------------------------------
            |
            | This config option allows you to define some classes which
            | will be applied by default to all icons within this set.
            |
            */

            'class' => '',

            /*
            |-----------------------------------------------------------------
            | Default Set Attributes
            |-----------------------------------------------------------------
            |
            | This config option allows you to define some attributes which
            | will be applied by default to all icons within this set.
            |
            */

            'attributes' => [
                // 'width' => 50,
                // 'height' => 50,
            ],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global Default Classes
    |--------------------------------------------------------------------------
    |
    | This config option allows you to define some classes which
    | will be applied by default to all icons.
    |
    */

    'class' => '',

    /*
    |--------------------------------------------------------------------------
    | Global Default Attributes
    |--------------------------------------------------------------------------
    |
    | This config option allows you to define some attributes which
    | will be applied by default to all icons.
    |
    */

    'attributes' => [
        // 'width' => 50,
        // 'height' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Fallback Icon
    |--------------------------------------------------------------------------
    |
    | This config option allows you to define a global fallback
    | icon when an icon in any set cannot be found. It can
    | reference any icon from any configured set.
    |
    */

    'fallback' => '',

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | These config options allow you to define some
    | settings related to Blade Components.
    |
    */

    'components' => [

        /*
        |----------------------------------------------------------------------
        | Disable Components
        |----------------------------------------------------------------------
        |
        | This config option allows you to disable Blade components
        | completely. It's useful to avoid performance problems
        | when working with large icon libraries.
        |
        */

        'disabled' => false,

        /*
        |----------------------------------------------------------------------
        | Default Icon Component Name
        |----------------------------------------------------------------------
        |
        | This config option allows you to define the name
        | for the default Icon class component.
        |
        */

        'default' => 'icon',

    ],

];
