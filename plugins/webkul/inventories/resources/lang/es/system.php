<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'No hay nada para comprobar la disponibilidad.',
        ],

        'cancel-move' => [
            'already-done' => 'No se puede cancelar un movimiento de existencias que se ha marcado como \'Hecho\'. Cree una devolución para revertir los movimientos que tuvieron lugar.',
        ],

        'unreserve-move' => [
            'already-done' => "No se puede anular la reserva de un movimiento de existencias que se ha marcado como 'Hecho'.",
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'La cantidad realizada para el producto ":product" no respeta la precisión de redondeo definida en la unidad de medida ":unit". Cambie la cantidad realizada o la precisión de redondeo de su unidad de medida.',
            'no-negative-quantities'     => 'No se permiten cantidades negativas',
            'missing-lot-serial-number'  => "Debe proporcionar un número de lote/serie para el producto:\n:products",
        ],

        'run-procurement' => [
            'no-rule-found'      => "No se ha encontrado ninguna regla para reabastecer \":product\" en \":location\".\nVerifique la configuración de las rutas en el producto.",
            'no-source-location' => '¡No hay ninguna ubicación de origen definida en la regla de existencias: :name!',
            'no-vendor-price'    => 'No hay ningún precio de proveedor coincidente para generar la orden de compra del producto :product (no hay proveedor definido, no se alcanza la cantidad mínima, fechas no válidas, ...). Vaya al formulario del producto y complete la lista de proveedores.',
        ],

        'return' => [
            'origin' => 'Devolución de :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'No se permite reservar una cantidad negativa.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'Debe establecerse la cantidad o la cantidad reservada.',
        'removal-strategy-not-implemented' => 'La estrategia de retirada :strategy no está implementada.',
        'unreserve-more-than-stock'        => 'No es posible anular la reserva de más productos de :name que los que tiene en existencias.',
    ],

    'product' => [
        'endless-loop-rule' => "Configuración de regla no válida, la siguiente regla provoca un bucle infinito: :name",
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'La cantidad realizada para el producto :product no respeta la precisión de redondeo definida en la unidad de medida :unit. Cambie la cantidad realizada o la precisión de redondeo de su unidad de medida.',
        'split-done-or-cancel'       => 'No se puede dividir un movimiento de existencias que se ha marcado como \'Hecho\' o \'Cancelado\'.',
        'split-draft'                => 'No se puede dividir un movimiento en borrador. Primero debe confirmarse.',
        'serial-already-assigned'    => 'El número de serie ya se ha asignado al producto: :product, número de serie: :serial_number',
    ],

    'rule' => [
        'delay-on'     => 'Retraso en :name',
        'days'         => '+ :days día(s)',
        'time-horizon' => 'Horizonte temporal',
    ],
];
