<?php

return [
    'action'       => 'Configurar la contabilidad para esta empresa',
    'notice'       => 'La contabilidad aún no está configurada para esta empresa. Use \'Configurar la contabilidad\' abajo para crear su plan de cuentas, diarios y ajustes predeterminados.',
    'results'      => [
        'failed' => [
            'title' => 'La configuración contable falló',
            'body'  => 'Un error inesperado detuvo la configuración y no se creó nada para esta empresa. Inténtelo de nuevo o contacte con su administrador. Los detalles técnicos se han registrado.',
        ],

        'no-company' => [
            'title' => 'No hay ninguna empresa seleccionada',
            'body'  => 'Seleccione la empresa para la que desea configurar la contabilidad e inténtelo de nuevo.',
        ],
    ],
];
