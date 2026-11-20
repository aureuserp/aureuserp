<?php

return [
    'breadcrumb' => 'Branding',
    'title'      => 'Branding',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Branding',
    ],

    'form' => [
        'tabs' => [
            'colors' => 'Brand Colors',
            'logo'   => 'Logo & Favicon',
        ],

        'sections' => [
            'colors' => [
                'title'       => 'Brand Colors',
                'description' => 'Set the colors used across the admin and customer panels. Click the info icon on any color to preview how it looks before saving.',
            ],
            'palettes' => [
                'title'       => 'Suggested Color Palettes',
                'description' => 'Choose from our pre-designed color palettes or create your own.',
            ],
            'logo' => [
                'title'       => 'Logo & Favicon',
                'description' => 'Override the logos and favicon used across the admin and customer panels. Leave a field empty to keep the default.',
            ],
        ],

        'fields' => [
            'light-logo'         => 'Light Logo',
            'light-logo-helper'  => 'Shown on light backgrounds. Replaces the default logo.',
            'dark-logo'          => 'Dark Logo',
            'dark-logo-helper'   => 'Shown when dark mode is enabled.',
            'favicon'            => 'Favicon',
            'favicon-helper'     => 'Browser tab icon.',
            'logo-height'        => 'Logo Height',
            'logo-height-helper' => 'A CSS height value, e.g. 2rem or 40px.',
            'primary-color'      => 'Primary',
            'gray-color'         => 'Gray',
            'danger-color'       => 'Danger',
            'info-color'         => 'Info',
            'success-color'      => 'Success',
            'warning-color'      => 'Warning',
        ],

        'palettes' => [
            'coral'       => 'Coral Red',
            'ocean'       => 'Ocean Blue',
            'forest'      => 'Forest Green',
            'purple'      => 'Purple Dream',
            'modern'      => 'Modern Gray',
            'apply'       => 'Use this',
            'custom-hint' => 'Need something different? Set your own colors with the pickers above.',
        ],

        'preview' => [
            'title'             => 'Live preview',
            'heading'           => 'Color Preview',
            'description'       => 'See how your selected colors will look across the application.',
            'hint'              => 'Click the info icon on any color to see a live preview of how your selected colors look across the entire application.',
            'shade-note'        => 'Shade 600 (used for buttons)',
            'light'             => 'Light theme',
            'dark'              => 'Dark theme',
            'search'            => 'Search...',
            'primary-button'    => 'Primary Button',
            'secondary-button'  => 'Secondary Button',
            'alert'             => 'This is an info message.',
            'card-title'        => 'Card Title',
            'card-body'         => 'This is sample content to show how text and surfaces look with your selected colors.',
            'link'              => 'View details',
            'input-label'       => 'Heading Text',
            'input-placeholder' => 'Input field',

            'nav' => [
                'dashboard' => 'Dashboard',
                'products'  => 'Products',
                'orders'    => 'Orders',
                'customers' => 'Customers',
                'settings'  => 'Settings',
            ],

            'status' => [
                'success' => 'Success',
                'info'    => 'Info',
                'warning' => 'Warning',
                'danger'  => 'Danger',
            ],
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Reset to default',
        ],
    ],
];
