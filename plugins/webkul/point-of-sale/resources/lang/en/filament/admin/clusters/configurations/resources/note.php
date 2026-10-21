<?php

return [
    'navigation' => [
        'title' => 'Note Models',
        'group' => 'Point of Sale',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'  => 'Note',
                    'color' => 'Colour',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'Note',
            'color'   => 'Colour',
            'company' => 'Company',
        ],
    ],
];
