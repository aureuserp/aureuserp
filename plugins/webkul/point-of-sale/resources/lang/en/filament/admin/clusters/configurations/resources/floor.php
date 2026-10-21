<?php

return [
    'navigation' => [
        'title' => 'Floors',
        'group' => 'Point of Sale',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'                => 'Name',
                    'company'             => 'Company',
                    'background-color'    => 'Background Colour',
                    'background-image'    => 'Background Image',
                    'configs'             => 'Point of Sale',
                    'configs-helper-text' => 'Restaurant-mode terminals that use this floor.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'             => 'Name',
                    'background-color' => 'Background Colour',
                    'company-name'     => 'Company',
                    'configs'          => 'Point of Sale',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Name',
            'tables'           => 'Tables',
            'configs'          => 'Point of Sale',
            'background-color' => 'Background Colour',
            'company'          => 'Company',
        ],
    ],
];
