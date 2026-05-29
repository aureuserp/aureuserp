<?php

return [
    'label'             => 'Validar',
    'modal-heading'     => '¿Crear pedido pendiente?',
    'modal-description' => 'Cree un pedido pendiente si los productos restantes se procesarán más adelante. De lo contrario, no genere un pedido pendiente.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'Sin pedido pendiente',
        ],
    ],

    'notification' => [
        'error' => [
            'title' => 'Validación fallida',
        ],

        'warning' => [
            'lines-missing' => [
                'title' => 'No hay cantidades reservadas',
                'body'  => 'No hay cantidades reservadas para la transferencia.',
            ],

            'no-quantities-reserved' => [
                'title' => 'No hay cantidades reservadas',
                'body'  => 'No hay cantidades reservadas para la transferencia.',
            ],

            'lot-missing' => [
                'title' => 'Proporcione lote/número de serie',
                'body'  => 'Debe proporcionar un lote/número de serie para los productos :products.',
            ],

            'serial-qty' => [
                'title' => 'Número de serie ya asignado',
                'body'  => 'El número de serie ya ha sido asignado a otro producto.',
            ],

            'partial-package' => [
                'title' => 'No se puede mover el mismo contenido de paquete',
                'body'  => 'No puede mover el mismo contenido de paquete más de una vez dentro de una sola transferencia ni dividir el paquete entre dos ubicaciones.',
            ],
        ],
    ],
];
