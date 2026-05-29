<?php

return [
    'title' => 'Gestionar Trazabilidad',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Lotes y Números de Serie',
        'enable-lots-serial-numbers-helper-text'                 => 'Obtén trazabilidad completa desde proveedores hasta clientes',
        'configure-lots'                                         => 'Configurar Lotes',
        'enable-expiration-dates'                                => 'Fechas de Caducidad',
        'enable-expiration-dates-helper-text'                    => 'Establece fechas de caducidad en lotes y números de serie',
        'display-on-delivery-slips'                              => 'Mostrar en Albaranes de Entrega',
        'display-on-delivery-slips-helper-text'                  => 'Los lotes y números de serie aparecerán en los albaranes de entrega',
        'display-expiration-dates-on-delivery-slips'             => 'Mostrar Fechas de Caducidad en Albaranes de Entrega',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Las fechas de caducidad aparecerán en el albarán de entrega',
        'enable-consignments'                                    => 'Consignaciones',
        'enable-consignments-helper-text'                        => 'Asigna propietario a los productos almacenados',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Hay productos en existencias con seguimiento por lote/número de serie activado. ',
                'body'  => 'Primero desactiva el seguimiento en todos los productos antes de desactivar esta configuración.',
            ],
        ],
    ],
];
