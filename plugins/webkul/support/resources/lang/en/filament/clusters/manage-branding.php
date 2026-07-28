<?php

return [
    'breadcrumb' => 'Branding',
    'title'      => 'Branding',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Branding',
    ],

    'form' => [
        'sections' => [
            'logo' => [
                'title'       => 'Logo & Favicon',
                'description' => 'Override the logos, favicon and logo height used across the admin and customer panels. Leave a field empty to keep the default.',
            ],
            'colors' => [
                'title'       => 'Colors',
                'description' => 'Override the theme colors used across the admin and customer panels. Leave a color empty to keep the default.',
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
    ],

    'actions' => [
        'reset' => [
            'label' => 'Reset to default',
        ],
    ],
];
