<?php

return [
    'navigation' => [
        'title' => 'Preparation Printers',
        'group' => 'Point of Sale',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'         => 'Name',
                    'printer-type' => 'Printer Type',
                    'proxy-ip'     => 'Proxy IP',
                    'company'      => 'Company',
                    'categories'   => 'Printed Categories',
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
                    'printer-type' => 'Printer Type',
                    'proxy-ip'     => 'IP Address',
                    'company-name' => 'Company',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'printer-type' => 'Printer Type',
            'proxy-ip'     => 'Proxy IP',
            'categories'   => 'Categories',
            'company'      => 'Company',
        ],

        'filters' => [
            'printer-type' => 'Printer Type',
        ],
    ],
];
