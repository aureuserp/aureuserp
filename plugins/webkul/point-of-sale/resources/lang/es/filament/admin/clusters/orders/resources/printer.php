<?php

return [
    'navigation' => [
        'title' => 'Impresoras de preparación',
        'group' => 'Punto de venta',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'         => 'Nombre',
                    'printer-type' => 'Tipo de impresora',
                    'proxy-ip'     => 'IP del proxy',
                    'company'      => 'Empresa',
                    'categories'   => 'Categorías impresas',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General',

                'entries' => [
                    'name'         => 'Nombre',
                    'printer-type' => 'Tipo de impresora',
                    'proxy-ip'     => 'Dirección IP',
                    'company-name' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'printer-type' => 'Tipo de impresora',
            'proxy-ip'     => 'IP del proxy',
            'categories'   => 'Categorías',
            'company'      => 'Empresa',
        ],

        'filters' => [
            'printer-type' => 'Tipo de impresora',
        ],
    ],
];
