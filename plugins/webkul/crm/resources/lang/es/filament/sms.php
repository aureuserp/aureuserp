<?php

return [
    'counter' => ':characters caracteres, cabe en :parts SMS (:encoding)',

    'form' => [
        'international-helper' => 'Incluye el código de país (por ejemplo +91 98765 43210) o define un país en el registro.',
    ],

    'errors' => [
        'invalid-number' => 'Número no válido: define un país en el lead/oportunidad o incluye el código de país.',
        'not-configured' => 'No hay ninguna pasarela SMS configurada. Añade los datos en Ajustes → SMS.',
        'unreachable'    => 'No se pudo conectar con la pasarela SMS.',
    ],
];
