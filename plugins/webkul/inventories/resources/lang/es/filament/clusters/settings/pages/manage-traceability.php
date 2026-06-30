<?php

return [
    'title' => 'Gestionar trazabilidad',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Lotes y Números de Serie',
        'enable-lots-serial-numbers-helper-text'                 => 'Obtener trazabilidad completa desde proveedores hasta clientes.',
        'configure-lots'                                         => 'Configurar lotes',
        'enable-expiration-dates'                                => 'Fechas de vencimiento',
        'enable-expiration-dates-helper-text'                    => 'Establecer fechas de vencimiento en lotes y números de serie.',
        'display-on-delivery-slips'                              => 'Mostrar en Comprobantes de entrega',
        'display-on-delivery-slips-helper-text'                  => 'Los lotes y números de serie aparecerán en los comprobantes de entrega',
        'display-expiration-dates-on-delivery-slips'             => 'Mostrar Fechas de vencimiento en Comprobantes de entrega',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Las fechas de vencimiento aparecerán en el comprobante de entrega',
        'enable-consignments'                                    => 'Consignaciones',
        'enable-consignments-helper-text'                        => 'Asigna propietario a los productos almacenados',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Hay productos en existencias con seguimiento por lote/número de serie activado. ',
                'body'  => 'Desactivar primero el seguimiento en todos los productos antes de deshabilitar esta configuración.',
            ],
        ],
    ],
];
