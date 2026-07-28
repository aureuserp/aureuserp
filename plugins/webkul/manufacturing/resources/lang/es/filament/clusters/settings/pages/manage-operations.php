<?php

return [
    'title' => 'Gestionar operaciones',

    'form' => [
        'enable-work-orders' => [
            'label'       => 'Órdenes de trabajo',
            'helper-text' => 'Ejecutar operaciones en centros de trabajo designados.',
            'link-text'   => 'Configurar centros de trabajo',
        ],

        'enable-work-order-dependencies' => [
            'label'       => 'Dependencias de órdenes de trabajo',
            'helper-text' => 'Definir el orden en que deben procesarse las órdenes de trabajo. Activar esta función desde la pestaña Varios de cada LdM.',
        ],

        'enable-byproducts' => [
            'label'       => 'Subproductos',
            'helper-text' => 'Generar subproductos durante la producción (A + B → C + D).',
        ],
    ],
];
