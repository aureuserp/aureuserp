<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => "Algunas órdenes de trabajo ya están hechas, por lo que no se puede desplanificar esta orden de fabricación.\n\nSería una pena desperdiciar todo ese progreso, ¿verdad?",
            'work-orders-already-started' => "Algunas órdenes de trabajo ya han comenzado, por lo que no se puede desplanificar esta orden de fabricación.\n\nSería una pena desperdiciar todo ese progreso, ¿verdad?",
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'Seguimiento de tiempo: :name',
        'no-performance-productivity-loss' => "Debe definir al menos una pérdida de productividad sin archivar en la categoría 'Rendimiento'. Créela desde la configuración.",
    ],

    'work-center' => [
        'already-unblocked' => 'Ya se ha desbloqueado.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'Desbloquee el centro de trabajo para iniciar la orden de trabajo.',
        'already-done-or-cancelled'  => 'No se puede iniciar una orden de trabajo que ya está hecha o cancelada',
        'no-calendar-on-work-center' => 'No hay ningún calendario definido en el centro de trabajo :name.',
        'no-productivity-loss'       => "Debe definir al menos una pérdida de productividad en la categoría 'Productividad'. Créela desde la configuración.",
        'no-performance-loss'        => "Debe definir al menos una pérdida de productividad en la categoría 'Rendimiento'. Créela desde la configuración.",
        'impossible-to-plan'         => 'Imposible planificar la orden de trabajo. Compruebe la disponibilidad del centro de trabajo.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'No se puede tener :product como producto terminado y en los subproductos',
        'missing-lot-serial-number'                => 'Debe proporcionar un número de lote/serie para los productos y "consumirlos": :missing_products',
        'serial-number-already-produced'           => 'Este número de serie para el producto :product ya se ha producido',
        'byproduct-serial-number-already-produced' => 'El número de serie :number utilizado para el subproducto :product ya se ha producido',
        'component-serial-number-consumed'         => 'El número de serie :number utilizado para el componente :component ya se ha consumido',
        'components-availability'                  => [
            'available'     => 'Disponible',
            'not-available' => 'No disponible',
            'expected'      => 'Previsto :date',
        ],
    ],
];
