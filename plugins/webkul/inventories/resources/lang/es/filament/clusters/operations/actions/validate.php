<?php

return [
    'label'                 => 'Validar',
    'modal-heading'         => '¿Crear Pedido Pendiente?',
    'modal-description'     => 'Cree un pedido pendiente si los productos restantes se procesarán más tarde. Si no, no genere un pedido pendiente.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'No Crear Pedido Pendiente',
        ],
    ],

    'notification' => [
        'warning' => [
            'lines-missing' => [
                'title' => 'No hay cantidades reservadas',
                'body'  => 'No hay cantidades reservadas para la transferencia.',
            ],

            'lot-missing' => [
                'title' => 'Suministrar Lote/Número de Serie',
                'body'  => 'Debe suministrar un Lote/Número de Serie para los productos',
            ],

            'serial-qty' => [
                'title' => 'Número de Serie Ya Asignado',
                'body'  => 'El número de serie ya ha sido asignado a otro producto.',
            ],

            'partial-package' => [
                'title' => 'No se puede mover el mismo contenido del paquete',
                'body'  => 'No puede mover el mismo contenido del paquete más de una vez dentro de una sola transferencia ni dividir el paquete entre dos ubicaciones.',
            ],
        ],
    ],
];