<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Condición de pago actualizada',
            'body'  => 'La condición de pago se ha actualizado correctamente.',
        ],

        'validation-error' => [
            'title' => 'Error de validación',
            'body'  => 'El plazo de vencimiento debe tener al menos una línea de porcentaje y la suma de los porcentajes debe ser 100%.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Condición de pago eliminada',
                'body'  => 'La condición de pago se ha eliminado correctamente.',
            ],
        ],
    ],
];
