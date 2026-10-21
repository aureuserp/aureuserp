<?php

return [
    'navigation' => [
        'title' => 'PoS Product Categories',
        'group' => 'Point of Sale',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'    => 'Name',
                    'parent'  => 'Parent Category',
                    'company' => 'Company',
                    'color'   => 'Colour',
                    'image'   => 'Image',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'         => 'Name',
                    'parent-name'  => 'Parent Category',
                    'color'        => 'Colour',
                    'company-name' => 'Company',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'Name',
            'parent'  => 'Parent Category',
            'color'   => 'Colour',
            'company' => 'Company',
        ],

        'groups' => [
            'parent' => 'Parent Category',
        ],
    ],
];
