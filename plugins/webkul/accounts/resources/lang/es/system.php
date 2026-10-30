<?php

return [
    'move' => [
        'no-journal-found-title' => 'La contabilidad no está configurada',
        'no-journal-found'       => 'No se encontró ningún diario en la empresa :company para ninguno de estos tipos: :types. Configure primero un plan de cuentas para esta empresa.',
    ],

    'posting' => [
        'invalid-entry'         => 'El asiento contable no es válido: :field.',
        'unbalanced'            => 'El asiento contable :move no está cuadrado: débito :debit, crédito :credit.',
        'rejected-by-validator' => 'La contabilización fue rechazada: :reason',
        'move-not-postable'     => 'El asiento contable no se puede contabilizar: :reason',
        'move-not-reversible'   => 'El asiento contable :move no se puede revertir.',
        'duplicate-source'      => 'La clave de origen ya fue utilizada por el asiento contable :move con otras líneas.',
    ],

    'tax-formula' => [
        'empty'                => 'La fórmula no puede estar vacía.',
        'invalid-character'    => 'La fórmula contiene un carácter no válido: :character',
        'unexpected-token'     => 'La fórmula contiene un elemento inesperado: :token',
        'unexpected-end'       => 'La fórmula termina de forma inesperada.',
        'unclosed-parenthesis' => 'La fórmula contiene un paréntesis sin cerrar.',
        'unknown-variable'     => 'Variable desconocida ":variable". Solo están disponibles estas variables: :variables',
        'unknown-function'     => 'Función desconocida ":function". Solo están disponibles estas funciones: :functions',
    ],
];
